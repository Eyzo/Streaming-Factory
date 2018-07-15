<?php 
namespace Framework\Middleware;

use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Server\RequestHandlerInterface; 
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Psr\Http\Message\ResponseInterface;
use Framework\Session\Sessioninterface;
use  Framework\Database\NoRecordException;

class NoRecordExceptionMiddleware implements MiddlewareInterface
{
	

 	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler):ResponseInterface
 	{
 		try
 		{
 			return $handler->handle($request);	
 		}
 		catch(NoRecordException $exception)
 		{
 			
 			
 			return new RedirectResponse('/streaming');

 		}
 		
 	}

}