<?php


use Phinx\Migration\AbstractMigration;

class PostGenreTable extends AbstractMigration
{

    public function change()
    {
        $this->table('post_genre')
        ->addColumn('id_post','integer')
        ->addColumn('id_genre','integer')
        ->addForeignKey('id_post','posts','id',[])
        ->addForeignKey('id_genre','genre','id',[])
        ->create();
    }
}
