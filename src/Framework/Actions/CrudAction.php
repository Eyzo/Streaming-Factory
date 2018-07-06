<?php
namespace Framework\Actions;

use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Framework\Validator\Validator;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Framework\Database\Table;
use Framework\Database\Hydrator;

class CrudAction
{


    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * @var Router
     */
    protected $router;
    /**
     * @var Table
     */
    protected $table;

    /**
     * @var FlashService
     */
    protected $flash;

    /**
    *@var string
    */
    protected $viewPath;

    /**
    *@var string
    */
    protected $routePrefix;

    /**
    *@var array
    */
    protected $messages = [
    'create'=>'L\'élément a bien été créé',
    'edit'=>'L\'élément a bien été modifié'
    ];

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        Table $table,
        FlashService $flash
    ) {
    
        $this->renderer = $renderer;
        $this->router = $router;
        $this->table = $table;
        $this->flash = $flash;
    }

    public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);

        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string)$request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }

    public function index(Request $request): string
    {

        $params = $request->getQueryParams();
        $items = $this->table->findAll()->paginate(12, $params['p'] ?? 1);

        return $this->renderer->render($this->viewPath.'/index', compact('items'));
    }

    /**
     * Edite un élément
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request)
    {
        $item = $this->table->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->table->update($item->id, $this->getParams($request, $item));
                $this->flash->success($this->messages['edit']);
                return $this->redirect($this->routePrefix.'.index');
            }

            $errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(),$item);
        }

        $params = $this->formParams(compact('item', 'errors'));

        return $this->renderer->render($this->viewPath.'/edit', $params);
    }

    /**
     * Crée un nouvel élément
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request)
    {
        $item = $this->getNewEntity();

        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->table->insert($this->getParams($request, $item));
                $this->flash->success($this->messages['create']);
                return $this->redirect($this->routePrefix.'.index');
            }

            Hydrator::hydrate($request->getParsedBody(),$item);
            $errors = $validator->getErrors();
        }
        $params = $this->formParams(compact('item', 'errors'));

        return $this->renderer->render($this->viewPath.'/create', $params);
    }

    public function delete(Request $request)
    {
        $this->table->delete($request->getAttribute('id'));
        return $this->redirect($this->routePrefix.'.index');
    }

    protected function getParams(Request $request, $item) : array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, []);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getValidator(Request $request)
    {
        return new Validator($params = array_merge($request->getParsedBody(), $request->getUploadedFiles()));
    }

    protected function getNewEntity()
    {
        return [];
    }

    protected function formParams(array $params):array
    {

        return $params;
    }
}
