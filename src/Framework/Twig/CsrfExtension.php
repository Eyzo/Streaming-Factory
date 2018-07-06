<?php
namespace Framework\Twig;

use Framework\Middleware\CsrfMiddleware;

class CsrfExtension extends \Twig_Extension
{
    private $csrf;

    public function __construct(CsrfMiddleware $csrf)
    {
        $this->csrf = $csrf;
    }

    public function getFunctions():array
    {
        return [
        new \Twig_SimpleFunction('csrf_input', [$this,'csrfInput'], ['is_safe'=>['html']])
        ];
    }

    public function csrfInput():string
    {
        return '<input type="hidden" name="'.$this->csrf->getFormkey().'" value="'.$this->csrf->generateToken().'"/>';
    }
}
