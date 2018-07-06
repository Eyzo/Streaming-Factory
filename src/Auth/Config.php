<?php 
use App\Auth\DatabaseAuth;
use Framework\Auth\LoggedInMiddleware;
use App\Auth\Middleware\ForbiddenMiddleware;
use App\Auth\Twig\AuthTwigExtension;
use App\Auth\User;

return [
'twig.extensions'=>\DI\add([
	\DI\get(AuthTwigExtension::class)
	]),
'auth.login'=>'/login',
LoggedInMiddleware::class=>\DI\object()->constructor(\DI\get(DatabaseAuth::class)),
ForbiddenMiddleware::class=>\DI\object()->constructorParameter('loginPath',\DI\get('auth.login'))
];