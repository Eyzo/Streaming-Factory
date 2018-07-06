<?php 
namespace App\Blog\Table;

use Framework\Database\Table;
use App\Blog\Entity\Video;
use App\Blog\Entity\Lecteur_video;
use App\Blog\Entity\Episode;

class VideoTable extends Table 
{

	protected $table = 'video';

	protected $entity = Video::class;


	public function findVideoListeForEpisode(int $id_episode)
	{
		$lecteur_video = new LecteurVideoTable($this->pdo);
		$episode = new EpisodeTable($this->pdo);
		return $this->makeQuery()
		->join($lecteur_video->getTable().' as lv','v.id_lecteur = lv.id')
		->join($episode->getTable().' as e','v.episode_id = e.id')
		->select('v.*','lv.name','lv.slug')
		->where("e.id = $id_episode");
	}

	public function findDefaultVideoForEpisode(int $id_episode,int $defaultLecteur)
	{
		
		return $this->findVideoListeForEpisode($id_episode)->where("lv.id = $defaultLecteur");
	}

	public function findDefaultVideoForEpisodeSlug(int $id_episode,string $fdp)
	{
		
		return $this->findVideoListeForEpisode($id_episode)->where("lv.slug = '$fdp'");
	}
	

}