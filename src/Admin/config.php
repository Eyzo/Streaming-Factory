<?php

use App\Admin\AdminModule;
use App\Admin\Action\DashBoardAction;

return [
    'admin.prefix' => '/admin',
    'admin.widgets' => [],
    \App\Admin\AdminTwigExtension::class => \DI\object()->constructor(\DI\get('admin.widgets')),
    AdminModule::class => \DI\object()->constructorParameter('prefix', \DI\get('admin.prefix')),
    DashBoardAction::class => \DI\object()->constructorParameter('widgets', \DI\get('admin.widgets'))
];
