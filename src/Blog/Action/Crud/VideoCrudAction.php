<?php
namespace App\Blog\Action\Crud;

use App\Blog\Entity\Video;
use App\Blog\Table\EpisodeTable;
use App\Blog\Table\VideoTable;
use App\Blog\Table\LecteurVideoTable;
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

class VideoCrudAction extends CrudAction
{

    protected $viewPath = "@Blog/admin/video";

    protected $routePrefix = "blog.video.admin";

     private $episodeTable;

     private $lecteurTable;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        VideoTable $table,
        FlashService $flash,
        EpisodeTable $episodeTable,
        LecteurVideoTable $lecteurTable
    ) {
    
        parent::__construct($renderer, $router, $table, $flash);
        $this->episodeTable = $episodeTable;
        $this->lecteurTable = $lecteurTable;
        }


    /**
    *Page Index Episode
    */
    public function index(Request $request):string
    {
        $params = $request->getQueryParams();

       $id_episode = $request->getAttribute('id_episode');

        $episode = $this->episodeTable->find($id_episode);

        $items = $this->table->findVideoListeForEpisode($id_episode)->paginate(20,$params['p'] ?? 1);

        return $this->renderer->render($this->viewPath.'/index',compact('items','episode'));
    }

    /**
    *Page Edite Episode
    */
    public function edit(Request $request)
    {
        $item = $this->table->find($request->getAttribute('id')); 

        $id_episode = $request->getAttribute('id_episode');

        $episode = $this->episodeTable->find($id_episode);

        if ($request->getMethod() === 'POST') {

            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->table->update($item->id,$this->getParams($request,$item));
                $this->flash->success($this->messages['edit']);
                return $this->redirect($this->routePrefix.'.index',compact('id_episode'));
            }
            $errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(),$item);
        }

        $params['lecteur-videos'] = $this->lecteurTable->findList();

        $lecteurliste = [];

        foreach ($params['lecteur-videos'] as $key => $value) 
        {
            $lecteurliste[$key] = $value; 
        }

        $id = $request->getAttribute('id');
        
        return $this->renderer->render($this->viewPath.'/edit',compact('id_episode','episode','id','lecteurliste','item','errors'));
    }

    /**
    *Page Create Episode
    */
    public function create(Request $request)
    {
        $item = $this->getNewEntity();

        $id_episode = $request->getAttribute('id_episode');
        $episode = $this->episodeTable->find($id_episode);

        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);

            if ($validator->isValid()) {

                $params=$this->getParams($request,$item);
                $params['episode_id'] = $id_episode;

                $ok=$this->table->insert($params);
                $this->flash->success($this->messages['create']);
               return $this->redirect($this->routePrefix.'.index',compact('id_episode'));
            }

            Hydrator::hydrate($request->getParsedBody(),$item);
            $errors = $validator->getErrors();
        }

        $params['lecteur-videos'] = $this->lecteurTable->findList();

        $lecteurliste = [];

        foreach ($params['lecteur-videos'] as $key => $value) {
            $lecteurliste[$key] = $value; 
        }

         return $this->renderer->render($this->viewPath.'/create',compact('id_episode','episode','errors','item','lecteurliste'));
    }

    /**
    *Supprime un Episode
    */
    public function delete(Request $request)
    {
        $id_episode = $request->getAttribute('id_episode');
        
        $this->table->delete($request->getAttribute('id'));

        return $this->redirect($this->routePrefix.'.index',compact('id_episode'));
    }


    /**
    *Crée une instance de l'entité Episode
    */
    public function getNewEntity()
    {
        $video = new Video();
        return $video;
    }


        



    /**
    *Récupére les paramétres passé en Post 
    */
       protected function getParams(ServerRequestInterface $request, $item): array
    {
        $params = array_merge($request->getParsedBody());
        
        return $params = array_filter($params, function ($key) {
            return in_array($key, ['lien', 'id_lecteur',]);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
    *Condition de validation des paramétres Post
    */
   protected function getValidator(ServerRequestInterface $request)
    {
        return parent::getValidator($request)
            ->required('lien', 'id_lecteur')
            ->length('lien',10);
    }
       

}