<?php


use Phinx\Migration\AbstractMigration;

class AddVideoTable extends AbstractMigration
{

     
    public function change()
    {
        $this->table('video')
        ->addColumn('lien','text',['limit'=>Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG])
        ->addColumn('episode_id','integer')
        ->addForeignKey('episode_id','episode','id')
        ->create();
    }
}
