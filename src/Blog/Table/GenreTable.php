<?php 
namespace App\Blog\Table;

use App\Blog\Entity\Genre;
use Framework\Database\Table;

class GenreTable extends Table 
{

	protected $table = 'genre';

	protected $entity = Genre::class;

	public function findGenreName(int $id)
	{
		return $this->makeQuery()
		->where("g.id = $id")
		->fetch();
	}

	
}