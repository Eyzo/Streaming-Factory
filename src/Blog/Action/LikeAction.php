<?php 
namespace App\Blog\Action;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Response;
use Framework\Router;
use App\Vote\Vote;
use App\Blog\Table\PostTable;
use Framework\Actions\IpGet;
use Framework\Session\Sessioninterface;


class LikeAction 
{
	
	private $renderer;
	private $router;
	private $vote;
	private $postTable;
	private $session;
	
 use IpGet;

	public function __construct(RendererInterface $renderer,Router $router,Vote $vote,PostTable $postTable,Sessioninterface $session)
	{
		$this->renderer = $renderer;
		$this->router = $router;
		$this->vote = $vote;
		$this->postTable = $postTable;
		$this->session = $session;
	}

	public function __invoke(ServerRequestInterface $request)
	{
		$uri = $this->router->generateUri('blog.show',['slug_post' => $request->getAttribute('slug_post')]);

		$post = $this->postTable->findBy('slug',$request->getAttribute('slug_post'));

		$ip = $this->getip();

		$this->session->set('ip',$ip);

		if ($request->getMethod() != 'POST') 
		{
		return (new Response)->withStatus(403)->withHeader('Location',$uri);
		}
		if ($request->getAttribute('vote') == 1 )
		{
			$this->vote->like($post->id,$ip);
		}
		else 
		{
			$this->vote->dislike($post->id,$ip);
		}

		return (new Response)->withStatus(200)->withHeader('Location',$uri);

	}
}