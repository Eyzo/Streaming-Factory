<?php
namespace Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Response;

class NotFoundMiddleware
{

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        return new Response(404, [], 'Erreur 404');
    }
}
