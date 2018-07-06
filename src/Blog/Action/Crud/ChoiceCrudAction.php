<?php 
namespace App\Blog\Action\Crud;

use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use App\Blog\Table\VersionTable;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Blog\Table\PostTable;


class ChoiceCrudAction extends CrudAction
{

protected $viewPath = '@Blog/admin/choice';

protected $routePrefix = 'blog.admin';

private $postTable;

public function __construct(
		RendererInterface $renderer,
        Router $router,
        VersionTable $table,
        FlashService $flash,
        PostTable $postTable)
{
parent::__construct($renderer,$router,$table,$flash);
$this->postTable = $postTable;
}

public function index(Request $request):string
{

	$params = $request->getQueryParams();

	$post = $this->postTable->find($request->getAttribute('id_oeuvre'));

 	$items = $this->table->findAll()->paginate(12,$params['p'] ?? 1);

 	return $this->renderer->render($this->viewPath.'/index',compact('items','post'));
}

}