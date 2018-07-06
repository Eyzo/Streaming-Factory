<?php
namespace App\Blog\Action;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Blog\Table\CategoryTable;

class SearchShowAction
{

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PostTable
     */
    private $postTable;

    private $categoryTable;

    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, PostTable $postTable, CategoryTable $categoryTable)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->categoryTable = $categoryTable;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        


            $params=$request->getParsedBody();

        $paramfilter=array_filter($params,function ($key)
        {
            return in_array($key,['search']);
        },ARRAY_FILTER_USE_KEY);

        $paramSearch = '%'.$paramfilter['search'].'%';

        $params = $request->getQueryParams();

        $posts = $this->postTable->findPublicSearch($paramSearch)->fetchAll();
        $categories = $this->categoryTable->findAll();
        
        return $this->renderer->render('@Blog/index-search', compact('posts','categories')); 

}
}