<?php




return [
'env'=>\DI\env('ENV','production'),
'database.host'=>'localhost',
'database.username'=>'root',
'database.password'=>'',
'database.name'=>'monsupersite',
'views.path'=>dirname(__DIR__).'/views',
'twig.extensions'=>[
\DI\get(\Framework\Router\TwigRouterExtension::class),
\DI\get(\Framework\Twig\PagerFantaExtension::class),
\DI\get(\Framework\Twig\TextExtension::class),
\DI\get(\Framework\Twig\TimeExtension::class),
\DI\get(\Framework\Twig\FlashExtension::class),
\DI\get(\Framework\Twig\FormExtension::class),
\DI\get(\Framework\Twig\CsrfExtension::class),
\DI\get(\Framework\Twig\SessionExtension::class)
],
\Framework\Session\Sessioninterface::class=>\DI\object(Framework\Session\PHPSession::class),
\App\Blog\Action\Crud\EpisodeCrudAction::class=>\DI\object(),
\Framework\Middleware\CsrfMiddleware::class=>\DI\object()->constructor(\DI\get(\Framework\Session\Sessioninterface::class)),
\Framework\Router::class=>\DI\object(),
\Framework\Renderer\RendererInterface::class=>\DI\factory(\Framework\Renderer\TwigRendererFactory::class),
\PDO::class=> function (Psr\Container\ContainerInterface $c){
	return $pdo = new \PDO(
		'mysql:host='.$c->get('database.host').';dbname='.$c->get('database.name').';charset=utf8',
		$c->get('database.username'),
		$c->get('database.password'),
		[
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
		]
		);
},

	//MAILER
	'mail.to'=>'duhameltonyeyzo@gmail.com',
	Swift_Mailer::class => \DI\factory(\Framework\Factory\SwiftMailerFactory::class)
];



?>