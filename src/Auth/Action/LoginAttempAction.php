<?php 
namespace App\Auth\Action;

use Psr\Http\Message\ServerRequestInterface;
use App\Auth\DatabaseAuth;
use Framework\Session\FlashService;
use Framework\Router;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\Sessioninterface;


class LoginAttempAction 
{

	private $renderer;
	private $auth;
	private $router;
	private $session;

	use RouterAwareAction;

	public function __construct(RendererInterface $renderer,DatabaseAuth $auth,Sessioninterface $session,Router $router)
	{
		$this->renderer = $renderer;
		$this->auth = $auth;
		$this->router = $router;
		$this->session = $session;
	}

	public function __invoke(ServerRequestInterface $request)
	{
		$params = $request->getParsedBody();
		$user = $this->auth->Login($params['username'],$params['password']);
		if ($user) 
		{
			$path = $this->session->get('auth.redirect') ?: $this->router->generateUri('Admin');
			$this->session->delete('auth.redirect');
			return new RedirectResponse($path);
		}
		else
		{
			(new Flashservice($this->session))->error('Identifiant ou mot de passe incorrect');
			return $this->redirect('auth.login');
		}
		

	}




}