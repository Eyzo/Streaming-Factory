<?php
namespace App\Blog\Action\Crud;

use Framework\Actions\CrudAction;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use App\Blog\Table\VersionTable;
use Framework\Session\FlashService;
use App\Auth\User;

class VersionCrudAction extends CrudAction
{

    protected $viewPath = '@Blog/admin/version';
    
    protected $routePrefix = 'blog.version.admin';

    public function __construct(RendererInterface $renderer, Router $router, VersionTable $table, FlashService $flash)
    {

        parent::__construct($renderer, $router, $table, $flash);
    }

    protected function getParams(ServerRequestInterface $request, $item) : array
    {
        return $params = array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug']);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getValidator(ServerRequestInterface $request)
    {
        return parent::getValidator($request)
            ->required('name', 'slug')
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->unique('slug', $this->table->getTable(), $this->table->getPdo(), $request->getAttribute('id'))
            ->slug('slug');
    }
}