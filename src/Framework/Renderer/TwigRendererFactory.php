<?php
namespace Framework\Renderer;

use Framework\Renderer\TwigRenderer;
use Psr\Container\ContainerInterface;
use Twig\Extension\DebugExtension;

class TwigRendererFactory
{

    public function __invoke(ContainerInterface $container) : TwigRenderer
    {
        $env = $container->get('env');
        $views = $container->get('views.path');
        $loader= new \Twig_Loader_Filesystem($views);
        $twig= new \Twig_Environment($loader, ['debug'=>true]);
        $twig->addExtension(new DebugExtension());
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }


        return new TwigRenderer($loader, $twig);
    }
}
