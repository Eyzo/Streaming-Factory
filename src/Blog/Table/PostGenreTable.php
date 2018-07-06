<?php 
namespace App\Blog\Table;

use App\Blog\Entity\PostGenre;
use Framework\Database\Table;
use App\Blog\Table\GenreTable;
use App\Blog\Table\PostTable;

class PostGenreTable extends Table 
{

 protected $table = 'post_genre';

 protected $entity = PostGenre::class;

	public function findPostGenre(int $id)
	{
		$post = new PostTable($this->pdo);
		$genre = new GenreTable($this->pdo);
		return $query = 
		$this->makeQuery()
		->join($post->getTable().' as po','p.id_post = po.id')
		->join($genre->getTable().' as g','p.id_genre = g.id')
		->select('p.*,g.name,g.slug')
		->where("po.id = $id");


	}

}