<?php
namespace App\Blog\Action;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Blog\Table\GenreTable;
use App\Blog\Table\CategoryTable;

class GenreShowAction
{

    
      /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PostTable
     */
    private $postTable;

    private $genreTable;

    private $categoryTable;

    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, PostTable $postTable, GenreTable $genreTable,CategoryTable $categoryTable)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->genreTable = $genreTable;
        $this->categoryTable = $categoryTable;
    }

    public function __invoke(Request $request)
    {
        
        $params = $request->getQueryParams();

        $category = $this->categoryTable->findBy('slug', $request->getAttribute('slug_category'));

        $genre = $this->genreTable->findBy('slug', $request->getAttribute('slug_genre'));

        $categories = $this->categoryTable->findAll();

        $genres = $this->genreTable->findAll();

        $posts = $this->postTable
        ->findPublicForGenreCategory($genre->id,$category->id)
        ->paginate(12, $params['p'] ?? 1);

        return $this->renderer->render('@Blog/index-genre', compact('posts','categories','category','genres','genre'));
    }
}
