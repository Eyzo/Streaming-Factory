<?php
namespace App\Blog\Action;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Blog\Table\EpisodeTable;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostGenreTable;
use App\Blog\Table\GenreTable;
use App\Blog\Table\CommentsTable;

class PostShowAction
{

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var Router
     */
    private $router;
    /**
     * @var PostTable
     */
    private $postTable;

    /**
    *@var EpisodeTable
    */
    private $episodeTable;

    /**
    *@var CategoryTable
    */
    private $categoryTable;

    /**
    *@var PostGenreTable
    */
    private $postGenreTable; 

    /**
    *@var GenreTable
    */
    private $genreTable;


    /**
    *@var CommentsTable
    */
    private $commentsTable;


    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, Router $router, PostTable $postTable,EpisodeTable $episodeTable,CategoryTable $categoryTable,PostGenreTable $postGenreTable,GenreTable $genreTable,CommentsTable $commentsTable)
    {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postTable = $postTable;
        $this->episodeTable = $episodeTable;
        $this->categoryTable = $categoryTable;
        $this->postGenreTable = $postGenreTable;
        $this->genreTable = $genreTable;
        $this->commentsTable = $commentsTable;
       
    }

    public function __invoke(Request $request)
    {

        $params = $request->getQueryParams();

        $post = $this->postTable->findBy('slug',$request->getAttribute('slug_post'));

        $post = $this->postTable->findWithCategory($post->id); 

        $category = $this->categoryTable->findBy('id',$post->categoryId);
           
           
        if (preg_match('/saison [0-9]+/', $post->name)) 
        {
        
        $saisonPos=strpos($post->name,' saison');

        $name=substr($post->name,0,$saisonPos).'%';

        $saisons = $this->postTable->findPublicSearch($name)->fetchAll();

        }
        
        $comments = $this->commentsTable->FetchCommentsPost($post->id);

        

        $comments_by_id = [];

        foreach ($comments as $comment) 
        {
            
            $comments_by_id[$comment->id] = $comment;

        }

        foreach ($comments as $key => $comment) 
        {
            if ($comment->parent_id != 0) 
            {
                $comments_by_id[$comment->parent_id]->children[] = $comment;
                unset($comments[$key]);

            }
        }

     


        $post_genres = $this->postGenreTable->findPostGenre($post->id)->fetchAll();

        $episodes_vf = $this->episodeTable->findEpisodesPostVF($post->id)->fetchAll();

        $episodes_vostfr = $this->episodeTable->findEpisodesPostVOSTFR($post->id)->fetchAll();

        $categories = $this->categoryTable->findAll();

        $genres = $this->genreTable->findAll();
        

        return $this->renderer->render('@Blog/show',compact('post','episodes_vf','episodes_vostfr','category','categories','post_genres','genres','saisons','comments'));
    }
}
