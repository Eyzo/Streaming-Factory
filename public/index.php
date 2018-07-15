<?php
use GuzzleHttp\Psr7\ServerRequest;


chdir(dirname(__DIR__));

require dirname(__DIR__).'/vendor/autoload.php';



$app= (new Framework\App(dirname(__DIR__).'/config/config.php'))
->addModule(\App\Admin\AdminModule::class)
->addModule(\App\Blog\BlogModule::class)
->addModule(\App\Contact\ContactModule::class)
->addModule(\App\Auth\AuthModule::class);

$container = $app->getContainer();

$app->pipe(Framework\Middleware\TrailingSlashMiddleware::class)
->pipe(Framework\Middleware\NoRecordExceptionMiddleware::class)
->pipe(App\Auth\Middleware\ForbiddenMiddleware::class)
->pipe($container->get('admin.prefix'),Framework\Auth\LoggedInMiddleware::class)
->pipe(Framework\Middleware\MethodMiddleware::class)
->pipe(Framework\Middleware\CsrfMiddleware::class)
->pipe(Framework\Middleware\RouterMiddleware::class)
->pipe(Framework\Middleware\DispatcherMiddleware::class)
->pipe(Framework\Middleware\NotFoundMiddleware::class);

$response=$app->run(ServerRequest::fromGlobals());
Http\Response\send($response);