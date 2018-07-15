<?php 
namespace Framework\Twig;

class SessionExtension extends \Twig_Extension
{
	private $pdo;

	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function getFunctions():array
	{
		return [
			new \Twig_SimpleFunction('SessionExist',[$this,'SessionExist']),
			new \Twig_SimpleFunction('SelectVote',[$this,'SelectVote']),
			new \Twig_SimpleFunction('SessionGet',[$this,'SessionGet']),
			new \Twig_SimpleFunction('getClass',[$this,'getClass'])
		];
	}

	public function SessionExist(string $var)
	{
		return isset($_SESSION[$var]);
	}

	public function SessionGet(string $var)
	{
		return $_SESSION[$var];
	}

	public function SelectVote(int $post_id,string $ip)
	{
		$req = $this->pdo->prepare('SELECT * FROM votes WHERE post_id = :post_id AND ip = :ip');
		$req->bindValue(':post_id',$post_id);
		$req->bindValue(':ip',$ip);
		$req->execute();
		return $vote = $req->fetch();
	}

	/**
 	*Permet d'ajouter une classe is-liked ou is-disliked suivant un enregistrement
 	*@param $vote mixed false/StdClass
 	*/
 	public static function getClass($vote)
 	{
 		if ($vote)
 		{
 			return $vote->vote == 1 ? 'is-liked' : 'is-disliked';
 		}
 		
 		return null;
 	}
}