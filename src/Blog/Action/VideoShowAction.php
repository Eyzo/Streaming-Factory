<?php
namespace App\Blog\Action;

use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Blog\Table\PostTable;
use App\Blog\Table\EpisodeTable;
use App\Blog\Table\VideoTable;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\GenreTable;
use App\Blog\Table\VersionTable;

class VideoShowAction
{

    
      /**
     * @var RendererInterface
     */
    private $renderer;

    private $postTable;

    private $episodeTable;

    private $videoTable;

    private $categoryTable;

    private $genreTable;

    private $versionTable;

    use RouterAwareAction;

    public function __construct(RendererInterface $renderer,PostTable $postTable,EpisodeTable $episodeTable,VideoTable $videoTable,CategoryTable $categoryTable,GenreTable $genreTable,VersionTable $versionTable)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->episodeTable = $episodeTable;
        $this->videoTable = $videoTable;
        $this->categoryTable = $categoryTable;
        $this->genreTable = $genreTable;
        $this->versionTable = $versionTable;
    }

    public function __invoke(Request $request)
    {
        $params = $request->getQueryParams();

        $post = $this->postTable->findby('slug',$request->getAttribute('slug_post'));

        $categories = $this->categoryTable->findAll();

        $category = $this->categoryTable->findby('id',$post->categoryId);

            if (preg_match('/saison [0-9]+/', $post->name)) 
        {
        
        $saisonPos=strpos($post->name,' saison');

        $name=substr($post->name,0,$saisonPos).'%';

        $saisons = $this->postTable->findPublicSearch($name)->fetchAll();

        }

        $slug_episode = $request->getAttribute('slug_episode');

        $episode = $this->episodeTable->findby('slug',$slug_episode);

        preg_match('/episode-[0-9]+/',$slug_episode, $matches, PREG_OFFSET_CAPTURE);

        preg_match('/[0-9]+/',$matches[0][0], $matches2, PREG_OFFSET_CAPTURE);

        $episodePaginated = $this->episodeTable->findAllEpisodesPostVersionAsc($post->id,$episode->versionId)->paginate(1,$matches2[0][0]);

        $episode= $this->episodeTable->findEpisode($request->getAttribute('slug_episode'))->fetch();

        $version = $this->versionTable->findBy('id',$episode->versionId);

       $episodes_vf = $this->episodeTable->findEpisodesPostVF($post->id)->fetchAll();

        $episodes_vostfr = $this->episodeTable->findEpisodesPostVOSTFR($post->id)->fetchAll();

       $video = $this->videoTable->findDefaultVideoForEpisodeSlug($episode->id,$request->getAttribute('slug_lecteur'))->fetch();

        $genres = $this->genreTable->findAll();

        $video_liste = $this->videoTable->findVideoListeForEpisode($episode->id)->fetchAll();

        return $this->renderer->render('@Blog/index-video',compact('categories','category','post','episodes_vf','episodes_vostfr','episode','genres','video','video_liste','version','episodePaginated','saisons'));
    }
}