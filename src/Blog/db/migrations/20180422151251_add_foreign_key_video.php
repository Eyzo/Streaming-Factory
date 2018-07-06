<?php


use Phinx\Migration\AbstractMigration;

class AddForeignKeyVideo extends AbstractMigration
{

    public function change()
    {
        $this->table('video')
        ->addColumn('id_lecteur','integer')
        ->addForeignKey('id_lecteur','lecteur_video','id')
        ->update();
    }
}
