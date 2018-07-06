<?php 
namespace App\Blog\Table;

use Framework\Database\Table;
use App\Blog\Entity\Version;

class VersionTable extends Table 
{
	protected $entity = Version::class;

	protected $table = 'version';

}