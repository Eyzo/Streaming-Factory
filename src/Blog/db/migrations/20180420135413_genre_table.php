<?php


use Phinx\Migration\AbstractMigration;

class GenreTable extends AbstractMigration
{
  
    public function change()
    {
        $this->table('genre')
        ->addColumn('name','string')
        ->addColumn('slug','string')
        ->create();
    }
}
