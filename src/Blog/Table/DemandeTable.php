<?php 
namespace App\Blog\Table;

use Framework\Database\Table;
use App\Blog\Entity\Demande;

class DemandeTable extends Table
{
	
	protected $table = 'demande';
	protected $entity = Demande::class;

	public function insertDemande(string $content)
	{
		$req =$this->pdo->prepare('INSERT INTO demande SET message = :message');
		$req->bindValue(':message',$content);
		$req->execute();
	}

}