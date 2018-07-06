<?php
namespace App\Blog\Action;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Blog\Table\CategoryTable;

class PostIndexAction
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

    public function __invoke(Request $request)
    {
        $params = $request->getQueryParams();
        $posts = $this->postTable->findPublic()->paginate(12, $params['p'] ?? 1);
        $categories = $this->categoryTable->findAll();
    
        return $this->renderer->render('@Blog/index', compact('posts','categories'));
    }
}
