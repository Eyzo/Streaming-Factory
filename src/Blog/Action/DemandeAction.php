<?php 
namespace App\Blog\Action;

use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\DemandeTable;
use Framework\Validator\Validator;
use Framework\Session\FlashService;

class DemandeAction
{

	private $renderer;
	private $categoryTable;
	private $demandeTable;
	private $flash;


	public function __construct(RendererInterface $renderer,CategoryTable $categoryTable,DemandeTable $demandeTable,FlashService $flash)
	{
		$this->renderer = $renderer;
		$this->categoryTable = $categoryTable;
		$this->demandeTable = $demandeTable;
		$this->flash = $flash;
	}

	public function __invoke(ServerRequestInterface $request)
	{
		if ($request->getMethod() === 'POST') 
		{
			return $this->postDemande($request);
		}

		return $this->index($request);
	}

	public function index(ServerRequestInterface $request)
	{
		$categories = $this->categoryTable->findAll();
		return $this->renderer->render('@Blog/demande_view',compact('categories'));
	}

	public function postDemande(ServerRequestInterface $request)
	{
		$categories = $this->categoryTable->findAll();

		$params = $request->getParsedBody();
		$validator = (new Validator($params))
		->required('content')
		->length('content',2);

		if ($validator->isValid()) 
		{
		$this->flash->success('La demande a bien était envoyée');
		$this->demandeTable->insertDemande($request->getParsedBody()['content']);

		return $this->renderer->render('@Blog/demande_view',compact('categories'));
		}

		$this->flash->error('Merci de corriger vos erreurs');
		$errors = $validator->getErrors();

		return $this->renderer->render('@Blog/demande_view',compact('categories','errors'));
	}


}