<?php 
namespace App\Blog\Table;

use App\Blog\Entity\Comments;
use Framework\Database\Table;

class CommentsTable extends Table
{

protected $table = 'comments';

protected $entity = Comments::class;

public function FetchAllComments()
{
	return $comments = $this->pdo->query('SELECT c.*,p.name FROM comments as c LEFT JOIN posts as p ON c.id_post = p.id ORDER BY date DESC')->fetchAll();
}

public function FetchCommentsPost(int $id_post)
{

return $this->pdo->query('SELECT c.* FROM comments as c WHERE id_post ='.$id_post)->fetchAll();

}

public function FetchComment(int $parent_id)
{

$req = $this->pdo->prepare('SELECT c.* FROM comments as c WHERE id = ?');
$req->execute([$parent_id]);
return $comment = $req->fetch();

}


public function insertCommentsPost(string $content,int $parent_id,int $id_post,int $depth)
{
	$this->pdo->prepare('INSERT INTO comments SET content = ?,date = NOW(),parent_id = ?,id_post = ?,depth = ?')->execute([$content,$parent_id,$id_post,$depth]);
}

public function deleteComment(int $id)
{
  $this->pdo->prepare('DELETE FROM comments WHERE id = ?')->execute([$id]);
}


}