<?php
namespace App\Blog\Action\Crud;

use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use App\Blog\Table\PostGenreTable;
use App\Blog\Table\PostTable;
use Framework\Session\FlashService;
use Framework\Validator\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Database\Hydrator;
use App\Blog\Entity\PostGenre;
use App\Blog\Table\GenreTable;

class PostGenreCrudAction extends CrudAction
{

    protected $viewPath = "@Blog/admin/postgenre";

    protected $routePrefix = "blog.postgenre.admin";

    private $postTable;

    private $genreTable;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostGenreTable $table,
        FlashService $flash,
        PostTable $postTable,
        GenreTable $genreTable
    ) {
    
        parent::__construct($renderer, $router, $table, $flash);
        $this->postTable = $postTable;
        $this->genreTable = $genreTable;
        }


    /**
    *Page Index Episode
    */
    public function index(Request $request):string
    {
        $params = $request->getQueryParams();

       $id_oeuvre = $request->getAttribute('id_oeuvre');

        $post = $this->postTable->find($id_oeuvre);

        $items = $this->table->findPostGenre($id_oeuvre)->fetchAll();

        return $this->renderer->render($this->viewPath.'/index',compact('items','post'));
    }

 
    public function create(Request $request)
    {
        $item = $this->getNewEntity();

        $id_oeuvre = $request->getAttribute('id_oeuvre');

        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);

            if ($validator->isValid()) {

                $params=$this->getParams($request,$item);
                $params['id_post'] = $id_oeuvre;

                $ok=$this->table->insert($params);
                $this->flash->success($this->messages['create']);
               return $this->redirect($this->routePrefix.'.index',compact('id_oeuvre'));
            }

            Hydrator::hydrate($request->getParsedBody(),$item);
            $errors = $validator->getErrors();
        }

        $params['id_genre'] = $this->genreTable->findList();

        $genreliste = [];

        foreach ($params['id_genre'] as $key => $value) {
            $genreliste[$key] = $value; 
        }

         return $this->renderer->render($this->viewPath.'/create',compact('id_oeuvre','errors','item','genreliste'));
    }

    

    public function delete(Request $request)
    {

        $id_oeuvre = $request->getAttribute('id_oeuvre');

        $id = $request->getAttribute('id');
        
        $this->table->deletePostGenre($id_oeuvre,$id);

        return $this->redirect($this->routePrefix.'.index',compact('id_oeuvre'));
    }



    public function getNewEntity()
    {
        $PostGenre = new PostGenre();
        return $PostGenre;
    }


        



 
       protected function getParams(ServerRequestInterface $request, $item): array
    {
        $params = array_merge($request->getParsedBody());
        
        return $params = array_filter($params, function ($key) {
            return in_array($key, ['id_genre']);
        }, ARRAY_FILTER_USE_KEY);
    }

  
   protected function getValidator(ServerRequestInterface $request)
    {
        return parent::getValidator($request)
            ->required('id_genre');
    }
    
       

}