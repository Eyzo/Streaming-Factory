<?php 
namespace App\Auth\Action;

use Psr\Http\Message\ServerRequestInterface;
use Framework\Renderer\RendererInterface;
use App\Auth\DatabaseAuth;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;

class LogoutAction 
{

	private $renderer;
	private $flash;

	public function __construct(RendererInterface $renderer,DatabaseAuth $auth,FlashService $flash)
	{
		$this->renderer = $renderer;
		$this->auth = $auth;
		$this->flash = $flash;
	}

	public function __invoke(ServerRequestInterface $request)
	{
		$this->auth->logout();
		$this->flash->success('Vous êtes maintenant déconnecté');
		return new RedirectResponse('/');
	}
	
}