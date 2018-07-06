<?php
namespace App\Admin;

use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use App\Admin\Action\DashBoardAction;
use App\Admin\AdminTwigExtension;
use Framework\Renderer\TwigRenderer;

class AdminModule extends Module
{

    const DEFINITIONS = __DIR__.'/config.php';

    public function __construct(RendererInterface $renderer, Router $router, AdminTwigExtension $adminTwigExtension, string $prefix)
    {

        $renderer->addPath('Admin', __DIR__.'/views');
        $router->get($prefix, DashBoardAction::class, 'Admin');

        if ($renderer instanceof TwigRenderer) {
            $renderer->getTwig()->addExtension($adminTwigExtension);
        }
    }
}
