<?php


use Phinx\Migration\AbstractMigration;

class AddDateToEpisode extends AbstractMigration
{

    public function change()
    {
        $this->table('episode')
        ->addColumn('created_at','datetime')
        ->update();
    }
}
