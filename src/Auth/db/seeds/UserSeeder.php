<?php


use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $this->table('users')
        ->insert([
            'username'=>'Eyzo',
            'email' =>'duhameltonyeyzo@gmail.com',
            'password'=>password_hash('berserk62155',PASSWORD_DEFAULT)
        ])
        ->save();
    }
}
