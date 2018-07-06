<?php


use Phinx\Migration\AbstractMigration;

class ForeignKeyVersion extends AbstractMigration
{

    public function change()
    {
         $this->table('episode')
        ->addForeignKey('id_version','version','id')
        ->update();
    }
}
