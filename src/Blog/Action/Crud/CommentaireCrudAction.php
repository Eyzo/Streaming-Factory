<?php 
namespace App\Blog\Action\Crud;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Blog\Table\CommentsTable;
use Framework\Actions\RouterAwareAction;
USE Framework\Router;

class CommentaireCrudAction
{
	
	private $commentTable;
	private $renderer;
	private $router;

	use RouterAwareAction;

	public function __construct(RendererInterface $renderer,CommentsTable $commentTable,Router $router)
	{
		$this->renderer = $renderer;
		$this->commentsTable =$commentTable;
		$this->router = $router;
	}

	public function __invoke(ServerRequestInterface $request)
	{
		if ($request->getMethod() === 'DELETE') 
		{
			return $this->delete($request);
		}
		return $this->index($request);
	}

	public function index(ServerRequestInterface $request)
	{
		$comments = $this->commentsTable->FetchAllComments();
		

		return $this->renderer->render('@Blog/admin/commentaire/index',compact('comments'));
	}

	public function delete(ServerRequestInterface $request)
	{	
		$this->commentsTable->deleteComment($request->getAttribute('id'));
		return $this->redirect('blog.admin.commentaire.index');
	}

}