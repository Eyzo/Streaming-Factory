<?php
namespace App\Blog\Action;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\GenreTable;

class AlphabetShowAction
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

    private $genreTable;

    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, PostTable $postTable, CategoryTable $categoryTable,GenreTable $genreTable)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->categoryTable = $categoryTable;
        $this->genreTable = $genreTable;
    }

    public function __invoke(Request $request)
    {

        $alphabettri = $request->getAttribute('alphabet');

        $alphabet = $request->getAttribute('alphabet').'%';
        
        $category = $this->categoryTable->findBy('slug', $request->getAttribute('slug_category'));

        $categories = $this->categoryTable->findAll();

        $genres = $this->genreTable->findAll();

        $params = $request->getQueryParams();

        $posts = $this->postTable
        ->findPublicForCategoryWithAlphabet($category->id,$alphabet)
        ->paginate(12,$params['p'] ?? 1);
        
        $page = $params['p'] ?? 1;

        return $this->renderer->render('@Blog/index-category', compact('posts', 'categories', 'category','alphabettri','genres','page'));
    }
}