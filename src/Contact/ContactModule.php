<?php 
namespace App\Contact;

use Framework\Module;
use Framework\Router;
use App\Contact\Action\ContactAction;
use Framework\Renderer\RendererInterface;

class ContactModule extends Module
{

	const DEFINITIONS = __DIR__.'/config/config.php';

	public function __construct(Router $router,RendererInterface $renderer)
	{
		$renderer->addPath('contact',__DIR__.'/views');

		$router->get('/contact',ContactAction::class,'contact');
		$router->post('/contact',ContactAction::class);

	}

}