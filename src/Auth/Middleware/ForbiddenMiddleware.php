<?php 
namespace App\Auth\Middleware;

use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Server\RequestHandlerInterface; 
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Psr\Http\Message\ResponseInterface;
use Framework\Auth\ForbiddenException;
use Framework\Session\Sessioninterface;

class ForbiddenMiddleware implements MiddlewareInterface
{
	private $loginPath;
	private $session;

	public function __construct(string $loginPath,Sessioninterface $session)
	{
		$this->loginPath = $loginPath;
		$this->session = $session;
	}

 	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler):ResponseInterface
 	{
 		try
 		{
 			return $handler->handle($request);	
 		}
 		catch(ForbiddenException $exception)
 		{
 			$this->session->set('auth.redirect',$request->getUri()->getPath());
 			(new Flashservice($this->session))->error('Vous devez posséder un compte pour accéder a cette page');
 			
 			return new RedirectResponse($this->loginPath);

 		}
 		
 	}

}