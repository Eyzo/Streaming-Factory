<?php
namespace Framework\Middleware;

use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Framework\Exception\CsrfInvalidException;

class CsrfMiddleware implements MiddlewareInterface
{

    private $formkey;

    private $sessionkey;

    private $session;

    private $limit;

    public function __construct(&$session, int $limit = 50, string $formkey = '_csrf', string $sessionkey = 'csrf')
    {
        $this->validSession($session);
        $this->session = &$session;
        $this->formkey = $formkey;
        $this->sessionkey = $sessionkey;
        $this->limit = $limit;
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) :ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST','PUT','DELETE'])) {
            $params = $request->getParsedBody() ?: [];
            if (!array_key_exists($this->formkey, $params)) {
                $this->reject();
            } else {
                $csrfListe = $this->session[$this->sessionkey] ?? [];
                if (in_array($params[$this->formkey], $csrfListe)) {
                    $this->useToken($params[$this->formkey]);
                    return $handler->handle($request);
                } else {
                    $this->reject();
                }
            }
        } else {
            return $handler->handle($request);
        }
    }

    public function generateToken():string
    {
        $token = bin2hex(random_bytes(16));
        $csrfListe = $this->session[$this->sessionkey] ?? [];
        $csrfListe[]=$token;
        $this->session[$this->sessionkey] = $csrfListe;
        $this->LimitTokens();
        return $token;
    }

    private function reject() :void
    {
        throw new CsrfInvalidException();
    }

    private function useToken($token) : void
    {
        $tokens = array_filter($this->session[$this->sessionkey], function ($t) use ($token) {
            return $token !==$t;
        });
        $this->session[$this->sessionkey] = $tokens;
    }

    private function limitTokens():void
    {
        $tokens = $this->session[$this->sessionkey] ?? [];
        if (count($tokens) >$this->limit) {
            array_shift($tokens);
        }
        $this->session[$this->sessionkey] = $tokens;
    }

    private function validSession($session)
    {
        if (!is_array($session) && !$session instanceof \ArrayAccess) {
            throw new \TypeError('la session passé au middleware CSRF n\'est pas traitable comme un tableau ');
        }
    }

    public function getFormkey()
    {
        return $this->formkey;
    }
}
