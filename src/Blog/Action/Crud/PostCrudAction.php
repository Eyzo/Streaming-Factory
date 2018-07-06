<?php
namespace App\Blog\Action\Crud;

use App\Blog\Entity\Post;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;
use App\Blog\PostUpload;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostCrudAction extends CrudAction
{

    protected $viewPath = "@Blog/admin/posts";

    protected $routePrefix = "blog.admin";

    /**
     * @var CategoryTable
     */
    private $categoryTable;

    private $postUpload;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flash,
        CategoryTable $categoryTable,
        PostUpload $postUpload
    ) {
    
        parent::__construct($renderer, $router, $table, $flash);
        $this->categoryTable = $categoryTable;
        $this->postUpload = $postUpload;
    }

        public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);

       

        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string)$request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        if (!empty($request->getParsedBody()['search'])) {
            return $this->search($request);
        }
        return $this->index($request);
    }

        public function search(Request $request): string
    {

        $params = $request->getQueryParams();
        $search = '%'.$request->getParsedBody()['search'].'%';
        $items = $this->table->findAllSearch($search)->fetchAll();

        return $this->renderer->render($this->viewPath.'/index', compact('items','search'));
    }

    public function delete(ServerRequestInterface $request)
    {
        $post = $this->table->find($request->getAttribute('id'));
        $this->postUpload->delete($post->image);
        return parent::delete($request);
    }

    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryTable->findList();
        return $params;
    }

    protected function getNewEntity()
    {
        $post = new Post();
        $post->created_at = new \DateTime();
        return $post;
    }

    protected function getParams(ServerRequestInterface $request, $post): array
    {
        $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());

        $image = $this->postUpload->upload($params['image'], $post->image);
        
        
        if ($image) 
        {
            $params['image'] = $image;
        }
        else
        {
            unset($params['image']);
        }
        
        $params = array_filter($params, function ($key) {
            return in_array($key, ['name', 'slug', 'content', 'created_at', 'category_id','image','published']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function getValidator(ServerRequestInterface $request)
    {
        $validator = parent::getValidator($request)
            ->required('content', 'name', 'slug', 'created_at', 'category_id')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->exists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())
            ->dateTime('created_at')
            ->extension('image', ['jpg','png'])
            ->slug('slug');
        if (is_null($request->getAttribute('id'))) {
            $validator->uploaded('image');
        }
            return $validator;
    }
}
