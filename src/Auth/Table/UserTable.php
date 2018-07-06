<?php 
namespace App\Auth\Table;

use Framework\Database\Table;
use App\Auth\User;

class UserTable extends Table
{

protected $table = 'users';

protected $entity = User::class;


}