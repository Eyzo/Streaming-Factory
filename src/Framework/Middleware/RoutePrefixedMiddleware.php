<?php 
namespace Framework\Middleware;

use Psr\Container\ContainerInterface;
use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class RoutePrefixedMiddleware implements MiddlewareInterface
{
	
	private $prefix;
	private $middleware;
	private $container;

	public function __construct(ContainerInterface $container,string $prefix,string $middleware)
	{
		$this->container = $container;
		$this->prefix = $prefix;
		$this->middleware = $middleware;
	}

	 public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
	 {
	 	$path = $request->getUri()->getPath();
	 	if (strpos($path,$this->prefix) === 0) 
	 	{
	 		return $this->container->get($this->middleware)->process($request,$handler);
	 	}
	 	return $handler->handle($request);
	 }

}