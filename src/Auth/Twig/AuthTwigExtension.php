<?php 
namespace App\Auth\Twig;

use App\Auth\DatabaseAuth;

class AuthTwigExtension extends \Twig_Extension
{
	private $auth;

	public function __construct(DatabaseAuth $auth)
	{
		$this->auth = $auth;
	}

	public function getFunctions():array
	{
	return [
	new \Twig_SimpleFunction('current_user',[$this->auth,'getUser'])
	];

	}

}