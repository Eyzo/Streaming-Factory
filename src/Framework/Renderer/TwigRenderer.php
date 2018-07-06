<?php
namespace Framework\Renderer;

use Framework\Renderer\RendererInterface;

class TwigRenderer implements RendererInterface
{

    private $loader;
    private $twig;


    public function __construct(\Twig_Loader_Filesystem $loader, \Twig_Environment $twig)
    {

        $this->loader=$loader;
        $this->twig=$twig;
    }

    public function addPath(string $namespace, ?string $path = null) : void
    {
        $this->loader->addPath($path, $namespace);
    }

    public function render(string $view, array $params = []) : string
    {
        return $this->twig->render($view.'.twig', $params);
    }

    public function addGlobal($key, $value) : void
    {
        $this->twig->addGlobal($key, $value);
    }

    public function getTwig()
    {
        return $this->twig;
    }
}
