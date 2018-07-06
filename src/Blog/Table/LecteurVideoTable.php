<?php 
namespace App\Blog\Table;

use Framework\Database\Table;
use App\Blog\Entity\Lecteur_video;

class LecteurVideoTable extends Table
{

	protected $table = 'lecteur_video';

	protected $entity = Lecteur_video::class;

} 