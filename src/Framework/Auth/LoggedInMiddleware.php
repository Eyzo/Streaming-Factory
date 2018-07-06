<?php
namespace Framework\Auth;

use Framework\Auth;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Server\MiddlewareInterface;

class LoggedInMiddleware implements MiddlewareInterface
{

    /**
     * @var Auth
     */
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

      public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        $user = $this->auth->getUser();
        if (is_null($user)) {
            throw new ForbiddenException();
        }
        return $handler->handle($request->withAttribute('user', $user));
    }
}
