<?php
namespace App\Admin\Action;

use Framework\Renderer\RendererInterface;
use App\Admin\AdminWidgetInterface;

class DashBoardAction
{

/**
*@var RendererInterface
*/
    private $renderer;

/**
*@var AdminWidgetInterface[]
*/
    private $widgets;

    public function __construct(RendererInterface $renderer, array $widgets)
    {
        $this->renderer = $renderer;
        $this->widgets = $widgets;
    }

    public function __invoke()
    {

        $widgets= array_reduce($this->widgets, function (string $html, AdminWidgetInterface $widget) {
            return $html.$widget->render();
        }, '');

        return $this->renderer->render('@Admin/dashboard', compact('widgets'));
    }
}
