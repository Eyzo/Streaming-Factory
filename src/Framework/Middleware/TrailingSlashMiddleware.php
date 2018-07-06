<?php
namespace Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Framework\Router;

class TrailingSlashMiddleware
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $uri = $request->getUri()->getPath();

        $router = $this->container->get(Router::class);
        
       

        if (!empty($uri) && $uri[-1] === "/" && strlen($uri)>1) {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }
        
        if ($uri === '/') {
            return (new Response())
            ->withStatus(200)
            ->withHeader('Location', '/blog');

        }
        return $next($request);
    }
}
