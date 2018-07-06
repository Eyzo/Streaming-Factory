<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Framework\Middleware\RoutePrefixedMiddleware;

class App implements RequestHandlerInterface
{

    /**
     * List of modules
     * @var array
     */
    private $modules = [];

    private $definition;

    private $container;

    private $middlewares = [];

    private $index = 0;

    public function __construct(string $definition)
    {
        $this->definition = $definition;
    }

    public function addModule(string $module):self
    {
        $this->modules[] = $module;
        return $this;
    }

    public function pipe(string $routeprefix,?string $middleware = null):self
    {
        if ($middleware === null) 
        {
           $this->middlewares[] = $routeprefix; 
        } 
        else 
        {
            $this->middlewares[] = new RoutePrefixedMiddleware($this->getContainer(),$routeprefix,$middleware);
        }
        
        return $this;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            throw new Exception('Aucun middleware n\'a intercepté cette requête');
        } elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request,[$this,'handle']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }


    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }

        return $this->handle($request);
    }

    public function getContainer() : ContainerInterface
    {
        if ($this->container === null) {
            $builder= new \DI\ContainerBuilder();
            $builder->addDefinitions($this->definition);
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }

            $this->container=$builder->build();
        }
      
        return $this->container;
    }

    private function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            if (is_string($this->middlewares[$this->index])) {
                $middleware =  $this->container->get($this->middlewares[$this->index]);
            }
            else
            {
                $middleware = $this->middlewares[$this->index];
            }
            
            $this->index++;
            return $middleware;
        }
        return null;
    }

    public function getModules():array
    {
        return $this->modules;
    }
}
