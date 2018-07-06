<?php 
namespace App\Contact\Action;

use Framework\Renderer\RendererInterface as Renderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Framework\Session\FlashService;
use Framework\Validator\Validator;
use Framework\Response\RedirectResponse;
use App\Blog\Table\CategoryTable;

class ContactAction
{
	private $to;

	private $renderer;

	private $flashService;

	private $mailer;

	private $categoryTable;

	public function __construct(string $to,Renderer $renderer,FlashService $flashService,\Swift_Mailer $mailer,CategoryTable $categoryTable)
	{
		$this->to = $to;
		$this->renderer = $renderer;
		$this->flashService = $flashService;
		$this->mailer = $mailer;
		$this->categoryTable = $categoryTable;
	}

	public function __invoke(Request $request)
	{
		$categories = $this->categoryTable->findAll();

		if ($request->getMethod() === 'GET') 
		{
			
			return $this->renderer->render('@contact/contact',compact('categories'));
		}

		$params = $request->getParsedBody();

		$validator = (new Validator($params))
		->required('name','email','content')
		->length('name',5)
		->email('email')
		->length('content',5);

		

		if ($validator->isValid()) 
		{
			$this->flashService->success('Merci pour votre message !');
			
			$message = new \Swift_Message('Formulaire de contact',$params['content']);
			$message->setBody($this->renderer->render('@contact/email/contact.text', $params));
            $message->addPart($this->renderer->render('@contact/email/contact.html', $params), 'text/html');
			$message->setTo($this->to);
			$message->setFrom($params['email']);
			$this->mailer->send($message);
			
			return new RedirectResponse((string)$request->getUri());
		}
		else
		{
			$this->flashService->error('Merci de corriger vos erreurs');
			$errors = $validator->getErrors();
			return $this->renderer->render('@contact/contact',compact('errors','categories'));
		}
	}
}