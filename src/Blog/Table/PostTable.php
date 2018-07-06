<?php
namespace App\Blog\Table;

use Framework\Database\Table;
use App\Blog\Entity\Post;
use Framework\Database\Query;
use App\Blog\Table\PostGenreTable;

class PostTable extends Table
{

    protected $entity = Post::class;

    protected $table = 'posts';

    public function findAll():Query
    {
        $category = new CategoryTable($this->pdo);
        return $this->makeQuery()
        ->join($category->getTable().' as c','c.id = p.category_id')
        ->select('p.*,c.name as category_name,c.slug as category_slug')
        ->order('p.created_at DESC');
    }

      public function findAllSearch(string $search)
    {
        return $this->findAll()->where("p.name LIKE '$search'");
    }

    public function findPublic():Query
    {
        return $this->findAll()
        ->where('p.published = 1')
        ->where('p.created_at < NOW()');
    }

    public function findPublicSearch(string $search)
    {
        return $this->findPublic()->where("p.name LIKE '$search'");
    }

    public function findPublicForCategory(int $categoryId):Query
    {
        return $this->findPublic()->where("p.category_id = $categoryId");
    }

     public function findPublicForCategoryWithAlphabet(int $categoryId,string $alphabet):Query
    {
        return $this->findPublicForCategory($categoryId)->where("p.name LIKE '$alphabet'");
    }

    public function findWithCategory(int $postId):Post
    {
        return $this->findPublic()->where("p.id = $postId")->fetch();
    }


    public function findAllWithGenreCategory()
    {
        $category = new CategoryTable($this->pdo);
        $PostGenre = new PostGenreTable($this->pdo);
        return $this->makeQuery()
        ->join($category->getTable().' as c','c.id = p.category_id')
        ->join($PostGenre->getTable().' as pg','p.id = pg.id_post')
        ->select('p.*,c.name as category_name,c.slug as category_slug')
        ->order('p.created_at DESC');
    }

    public function findPublicWithGenreCategory()
    {
        return $this->findAllWithGenreCategory()
        ->where('p.published = 1')
        ->where('p.created_at < NOW()');
    }

    public function findPublicForGenreCategory(int $genreId,int $categoryId)
    {
        return $this->findPublicWithGenreCategory()
        ->where("pg.id_genre = $genreId")
        ->where("p.category_id = $categoryId");
    }
}
