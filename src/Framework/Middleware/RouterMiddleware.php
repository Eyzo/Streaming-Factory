<?php
namespace Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Response;
use Framework\Router;

class RouterMiddleware
{
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $router = $this->router;
        $route = $router->match($request);
        if (is_null($route)) {
            return $next($request);
        }
        $params = $route->getParams();
        $request = array_reduce(
            array_keys($params),
            function ($request, $key) use ($params) {
                return $request->withAttribute($key, $params[$key]);
            },
            $request
        );
        $request = $request->withAttribute(get_class($route), $route);
        return $next($request);
    }
}
