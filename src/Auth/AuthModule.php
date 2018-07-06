<?php 
namespace App\Auth;

use Framework\Module;
use Psr\Container\ContainerInterface;
use Framework\Router;
use App\Auth\Action\LoginAction;
use Framework\Renderer\RendererInterface;
use App\Auth\Action\LoginAttempAction;
use App\Auth\Action\LogoutAction;

class AuthModule extends Module 
{
	const DEFINITIONS = __DIR__.'/config.php';
    const MIGRATIONS = __DIR__.'/db/migrations';
    const SEEDS = __DIR__.'/db/seeds';

	public function __construct(ContainerInterface $container)
	{
		$renderer = $container->get(RendererInterface::class);
		$renderer->addPath('auth',__DIR__.'/views');

		$router = $container->get(Router::class);
        $router->get($container->get('auth.login'), LoginAction::class, 'auth.login');
        $router->post($container->get('auth.login'), LoginAttempAction::class);
        $router->post('/logout', LogoutAction::class,'auth.logout');
	}


}