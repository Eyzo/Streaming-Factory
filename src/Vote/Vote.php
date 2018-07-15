<?php
namespace App\Vote;

 class Vote
 {

 	private $pdo;
 	private $former_vote;

 	public function __construct(\PDO $pdo)
 	{
 		$this->pdo = $pdo;
 	} 

 	private function vote(int $post_id,string $ip,int $vote)
 	{
 		$req = $this->pdo->prepare('SELECT id,vote from votes WHERE post_id = :post_id AND ip = :ip');
 		$req->bindValue(':post_id',$post_id);
 		$req->bindValue(':ip',$ip);
 		$req->execute();
 		$vote_records = $req->fetch();
 		if ($vote_records) 
 		{
 		 if ($vote_records->vote == $vote) 
 		 {
 		 	return false;
 		 }
 		 $this->former_vote = $vote_records;
 		 $this->pdo->prepare("UPDATE votes SET vote = ? WHERE {$vote_records->id}")->execute([$vote]);
 		 return true;
		} 

 		$req = $this->pdo->prepare('INSERT INTO votes SET post_id = :post_id , ip = :ip , vote = :vote');
 		$req->bindValue(':post_id',$post_id);
 		$req->bindValue(':ip',$ip);
 		$req->bindValue(':vote',$vote);
 		$req->execute();

 		return true;
 	}

 	public function like(int $post_id,string $ip)
 	{
 		if ($this->vote($post_id,$ip,1)) 
 		{
 			$sql_part = "";
 			if ($this->former_vote) 
 			{
 				$sql_part = ", dislike_count = dislike_count - 1";
 			}
 			$this->pdo->query("UPDATE posts SET like_count = like_count + 1 $sql_part WHERE id = $post_id");
 		}
 	}

 	public function dislike(int $post_id,string $ip)
 	{
 		if ($this->vote($post_id,$ip,-1)) 
 		{
 			$sql_part = "";
 			if ($this->former_vote) 
 			{
 				$sql_part = ", like_count = like_count - 1";
 			}
 			$this->pdo->query("UPDATE posts SET dislike_count = dislike_count + 1 $sql_part WHERE id = $post_id");
 		}
 	}

 	public function updateCount(int $post_id)
 	{
 		$req = $this->pdo->prepare("SELECT COUNT(id) as count FROM votes WHERE post_id = ? GROUP BY vote");
 		$req->execute([$post_id]);
 		$votes = $req->fetchAll();
 		$counts = [
 			'-1' => 0,
 			'1' => 0
 		];
 		foreach ($votes as $vote) 
 		{
 			$counts[$vote ->vote] = $vote->count;
		}
		$req =  $this->pdo->query("UPDATE posts SET like_count = {$counts{1}},dislike_count = {$counts{-1}} WHERE id = $post_id");
		return true;
	}

 } 