<?php
namespace App\Blog\Action;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Blog\Table\GenreTable;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\EpisodeTable;
use App\Blog\Table\VideoTable;
use App\Blog\Table\VersionTable;

class EpisodeShowAction
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

    private $episodeTable;

    private $videoTable;

    private $versionTable;

    private $defaultLecteur = 1;

    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, PostTable $postTable, GenreTable $genreTable,CategoryTable $categoryTable,EpisodeTable $episodeTable,VideoTable $videoTable,VersionTable $versionTable)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->genreTable = $genreTable;
        $this->categoryTable = $categoryTable;
        $this->episodeTable = $episodeTable;
        $this->videoTable = $videoTable;
        $this->versionTable = $versionTable;
    }

    public function __invoke(Request $request)
    {
        $params = $request->getQueryParams();

        $post = $this->postTable->findBy('slug',$request->getAttribute('slug_post'));

        $category = $this->categoryTable->findBy('id',$post->categoryId);

        $categories = $this->categoryTable->findAll();

         if (preg_match('/saison [0-9]+/', $post->name)) 
        {
        
        $saisonPos=strpos($post->name,' saison');

        $name=substr($post->name,0,$saisonPos).'%';

        $saisons = $this->postTable->findPublicSearch($name)->fetchAll();

        }

        $slug_episode = $request->getAttribute('slug_episode');

       $episode = $this->episodeTable->findBy('slug',$request->getAttribute('slug_episode'));

       if ($category->name === 'series' || $category->name === 'animes') 
    	{  

        preg_match('/episode-[0-9]+/',$slug_episode, $matches, PREG_OFFSET_CAPTURE);

        preg_match('/[0-9]+/',$matches[0][0], $matches2, PREG_OFFSET_CAPTURE);

        $episodePaginated = $this->episodeTable->findAllEpisodesPostVersionAsc($post->id,$episode->versionId)->paginate(1,$matches2[0][0]);
    	}

        $episode= $this->episodeTable->findEpisode($request->getAttribute('slug_episode'))->fetch();

        $version = $this->versionTable->findBy('id',$episode->versionId);

        $episodes_vf = $this->episodeTable->findEpisodesPostVF($post->id)->fetchAll();

        $episodes_vostfr = $this->episodeTable->findEpisodesPostVOSTFR($post->id)->fetchAll();

        $defaultVideo = $this->videoTable->findDefaultVideoForEpisode($episode->id,$this->defaultLecteur)->fetch();

     
        while ($defaultVideo === false) 
        {

            $this->defaultLecteur++;
            $defaultVideo = $this->videoTable->findDefaultVideoForEpisode($episode->id,$this->defaultLecteur)->fetch();
        }
	
        $video_liste = $this->videoTable->findVideoListeForEpisode($episode->id)->fetchAll();

        $genres = $this->genreTable->findAll();

        return $this->renderer->render('@Blog/episode-show', compact('post','categories','category','genres','episodes_vf','episodes_vostfr','defaultVideo','video_liste','version','episode','episodePaginated','saisons'));
    }
}