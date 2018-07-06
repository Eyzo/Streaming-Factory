<?php
namespace App\Blog\Action\Crud;

use App\Blog\Entity\Post;
use App\Blog\Table\EpisodeTable;
use App\Blog\Table\PostTable;
use App\Blog\Table\VersionTable;
use Framework\Actions\CrudAction;
use App\Blog\Entity\Episode;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Validator\Validator;
use App\Blog\PostUpload;
use Psr\Http\Message\ServerRequestInterface as Request;
use Framework\Database\Hydrator;

class EpisodeCrudAction extends CrudAction
{

    protected $viewPath = "@Blog/admin/episode";

    protected $routePrefix = "blog.episode.admin";

     private $postTable;

    private $versionTable;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        EpisodeTable $table,
        FlashService $flash,
        PostTable $postTable,
        VersionTable $versionTable
    ) {
    
        parent::__construct($renderer, $router, $table, $flash);
        $this->postTable = $postTable;
        $this->versionTable = $versionTable;
        }


    /**
    *Page Index Episode
    */
    public function index(Request $request):string
    {
        $params = $request->getQueryParams();

        $id_oeuvre = $request->getAttribute('id_oeuvre');

        $id_version = $request->getAttribute('id_version');

        $post = $this->postTable->find($id_oeuvre);

        $version = $this->versionTable->find($id_version);

        $items = $this->table->findAllEpisodesPostVersion($id_oeuvre,$id_version)->fetchAll();


        return $this->renderer->render($this->viewPath.'/index',compact('items','post','version'));
    }

    /**
    *Page Edite Episode
    */
    public function edit(Request $request)
    {
        $item = $this->table->find($request->getAttribute('id')); 

        $id_oeuvre = $request->getAttribute('id_oeuvre');
        $id_version = $request->getAttribute('id_version');
        $post = $this->postTable->find($id_oeuvre);

        if ($request->getMethod() === 'POST') {

            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->table->update($item->id,$this->getParams($request,$item));
                $this->flash->success($this->messages['edit']);
                return $this->redirect($this->routePrefix.'.index',compact('id_oeuvre','id_version'));
            }
            $errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(),$item);
        }

        $params = $this->formParams(compact('item','errors'));

            
            $id = $request->getAttribute('id');

            
        
        return $this->renderer->render($this->viewPath.'/edit',compact('id_oeuvre','id_version','post','id','item','errors'));
    }

    /**
    *Page Create Episode
    */
    public function create(Request $request)
    {
        $item = $this->getNewEntity();

        $id_oeuvre = $request->getAttribute('id_oeuvre');
        $id_version = $request->getAttribute('id_version');
        $post = $this->postTable->find($id_oeuvre);

        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);

            if ($validator->isValid()) {

                $params=$this->getParams($request,$item);
                $params['version_id'] = $id_version;
                $params['post_id'] = $id_oeuvre;

                $ok=$this->table->insert($params);
                $this->flash->success($this->messages['create']);
               return $this->redirect($this->routePrefix.'.index',compact('id_oeuvre','id_version'));
            }
            $errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(),$item);
        }
        
        return $this->renderer->render($this->viewPath.'/create',compact('id_oeuvre','id_version','post','item','errors'));
    }

    /**
    *Supprime un Episode
    */
    public function delete(Request $request)
    {
        $id_oeuvre = $request->getAttribute('id_oeuvre');
        $id_version = $request->getAttribute('id_version');

        $this->table->delete($request->getAttribute('id'));

        return $this->redirect($this->routePrefix.'.index',['id_oeuvre'=>$id_oeuvre,'id_version'=>$id_version]);
    }


    /**
    *Crée une instance de l'entité Episode
    */
    public function getNewEntity()
    {
        $episode = new Episode();
        $episode->createdAt = new \Datetime();
        return $episode;
    }

    /**
    *Récupére les paramétres passé en Post 
    */
       protected function getParams(ServerRequestInterface $request, $item): array
    {
        $params = array_merge($request->getParsedBody());
        
        return $params = array_filter($params, function ($key) {
            return in_array($key, ['episode_num', 'slug','created_at']);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
    *Condition de validation des paramétres Post
    */
   protected function getValidator(ServerRequestInterface $request)
    {
        return parent::getValidator($request)
            ->required('episode_num', 'slug','created_at')
            ->length('slug', 2, 100)
            ->dateTime('created_at')
            ->unique('slug', $this->table->getTable(), $this->table->getPdo(), $request->getAttribute('id'));
    }
       

}