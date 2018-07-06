<?php


use Phinx\Migration\AbstractMigration;

class EpisodeTable extends AbstractMigration
{

    public function change()
    {
        $this->table('episode')
        ->addColumn('episode_num','integer')
        ->addColumn('slug','string')
        ->addColumn('post_id','integer',['null'=>true])
        ->addForeignKey('post_id','posts','id',[])
        ->create();


    }
}
