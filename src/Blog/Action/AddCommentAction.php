<?php 
namespace App\Blog\Action;

use Psr\Http\Message\ServerRequestInterface;
use Framework\Renderer\RendererInterface;
use Framework\Actions\RouterAwareAction;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Framework\Router;
use App\Blog\Table\CommentsTable;
use App\Blog\Table\PostTable;

class AddCommentAction 
{

private $renderer;

private $router;

private $commentsTable;

private $postTable;

use RouterAwareAction;

public function __construct(RendererInterface $renderer,Router $router,CommentsTable $commentsTable,PostTable $postTable)
{
	$this->renderer = $renderer;
	$this->router = $router;
	$this->commentsTable = $commentsTable;
	$this->postTable = $postTable;
}

public function __invoke(ServerRequestInterface $request)
{
	$slug_post = $request->getAttribute('slug_post');

	if ($request->getMethod() === 'POST' && !empty($request->getParsedBody()['content'])) 
	{

		$content = $request->getParsedBody()['content'];

		$parent_id = isset($request->getParsedBody()['parent_id']) ? $request->getParsedBody()['parent_id'] : 0;

		$post = $this->postTable->findBy('slug',$slug_post);

		$depth = 0;

	

		if ($depth >= 3) 
		{
			
		}
		else
		{

			if ($parent_id != 0) 
			{
				$comment = $this->commentsTable->FetchComment($parent_id);

				if ($comment == false) 
				{
					throw new \Exception('Ce parent n\'exsite pas');
				}

				$depth = $comment->depth + 1;
			}


		$this->commentsTable->insertCommentsPost($content,$parent_id,$post->id,$depth);

		}
		
	}

	return $this->redirect('blog.show',['slug_post' => $slug_post]);
}

}