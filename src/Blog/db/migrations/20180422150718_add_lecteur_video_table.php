<?php


use Phinx\Migration\AbstractMigration;

class AddLecteurVideoTable extends AbstractMigration
{
    public function change()
    {
        $this->table('lecteur_video')
        ->addColumn('name','string')
        ->create();
    }
}
