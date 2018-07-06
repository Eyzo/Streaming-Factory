<?php 
namespace App\Blog\Table;

use Framework\Database\Table;
use App\Blog\Entity\Episode;
use Framework\Database\Query;
use App\Blog\Table\PostTable;
use App\Blog\Table\VersionTable;


class EpisodeTable extends Table
{
	
	protected $table = 'episode';

	protected $entity = Episode::class;



	public function findEpisodesPost(int $id)
	{
		$version = new VersionTable($this->pdo);
		$post = new PostTable($this->pdo);

		return $this->makeQuery()
		->join($post->getTable().' as p','e.post_id = p.id')
		->join($version->getTable().' as v','e.version_id = v.id')
		->select('e.*,p.slug as postSlug,v.slug as versionSlug')
		->order('e.episode_num DESC')
		->where("e.post_id = $id");
	}

	public function findEpisodesPostVF(int $id)
	{
		return $this->findEpisodesPost($id)->where("e.version_id = 1");
	}

	public function findEpisodesPostVOSTFR(int $id)
	{
		return $this->findEpisodesPost($id)->where("e.version_id = 2");
	}

		public function findEpisode(string $slug)
	{
		$version = new VersionTable($this->pdo);
		$post = new PostTable($this->pdo);

		return $this->makeQuery()
		->join($post->getTable().' as p','e.post_id = p.id')
		->join($version->getTable().' as v','e.version_id = v.id')
		->select('e.*,p.slug as postSlug,v.slug as versionSlug')
		->where("e.slug = '$slug'");
	}

	public function findAllEpisodesPostVersionAsc(int $id_post,int $id_version)
	{
		$version = new VersionTable($this->pdo);
		$post = new PostTable($this->pdo);
		

		return $this->makeQuery()
		->join($post->getTable().' as p','e.post_id = p.id')
		->join($version->getTable().' as v','e.version_id = v.id')
		->select('e.*,v.name as Version_Name','v.slug as versionSlug')
		->where("e.post_id = $id_post")
		->where("e.version_id = $id_version")
		->order(" e.episode_num DESC ");
	}

	public function findAllEpisodesPostVersionSaisonAsc(int $id_post,int $id_version,int $id_saison)
	{
		return $this->findAllEpisodesPostVersionAsc($id_post,$id_version)->where("e.saison_id = $id_saison");
	}

		public function findAllEpisodesPostVersionCount(int $id_post,int $id_version)
	{
			$version = new VersionTable($this->pdo);
		$post = new PostTable($this->pdo);

		return $this->makeQuery()
		->join($post->getTable().' as p','e.post_id = p.id')
		->join($version->getTable().' as v','e.version_id = v.id')
		->select('e.*,v.name as Version_Name','v.slug as versionSlug')
		->where("e.post_id = $id_post")
		->where("e.version_id = $id_version");
	}


	public function findAllEpisodesPostVersion(int $id_post,int $id_version)
	{
			$version = new VersionTable($this->pdo);
		$post = new PostTable($this->pdo);

		return $this->makeQuery()
		->join($post->getTable().' as p','e.post_id = p.id')
		->join($version->getTable().' as v','e.version_id = v.id')
		->select('e.*,v.name as Version_Name','v.slug as versionSlug')
		->where("e.post_id = $id_post")
		->where("e.version_id = $id_version")
		->order(" e.created_at Desc ");
	}

		public function findAllEpisodesSaison(int $id_saison)
	{
		$version = new VersionTable($this->pdo);
		$post = new PostTable($this->pdo);

		return $this->makeQuery()
		->join($post->getTable().' as p','e.post_id = p.id')
		->join($version->getTable().' as v','e.version_id = v.id')
		->select('e.*,v.name as version_name','v.slug as version_slug','s.slug as saison_slug','s.name as saison_name')
		->where("e.saison_id = $id_saison")
		->order(" e.episode_num Desc ");
	}



}