<?php
namespace Framework\Router;

class Route
{

    private $name;
    private $callback;
    private $params;

    public function __construct(string $name, $callback, array $params = [])
    {

        $this->name=$name;
        $this->callback=$callback;
        $this->params=$params;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function getParams() : array
    {
        return $this->params;
    }
}
