<?php 
namespace App\Blog\Action;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class LikeAction 
{
	
	private $renderer;

	public function __construct(RendererInterface $renderer)
	{
		$this->renderer = $renderer;
	}

	public function __invoke(ServerRequestInterface $request)
	{
		return 'ok';
	}
}