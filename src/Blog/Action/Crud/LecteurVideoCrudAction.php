<?php
namespace App\Blog\Action\Crud;

use Framework\Actions\CrudAction;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use App\Blog\Table\LecteurVideoTable;
use Framework\Session\FlashService;
use App\Auth\User;

class LecteurVideoCrudAction extends CrudAction
{

    protected $viewPath = '@Blog/admin/lecteurvideo';
    
    protected $routePrefix = 'blog.lecteurvideo.admin';

    public function __construct(RendererInterface $renderer, Router $router, LecteurVideoTable $table, FlashService $flash)
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