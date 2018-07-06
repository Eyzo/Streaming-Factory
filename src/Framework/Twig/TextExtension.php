<?php
namespace Framework\Twig;

use Pagerfanta\Pagerfanta;

class TextExtension extends \Twig_Extension
{

    public function getFilters(): array
    {
        return
        [
        new \Twig_SimpleFilter('excerpt', [$this,'excerpt'])
        ];
    }

    public function excerpt(?string $content, int $maxLenght = 100):string
    {
        if (is_null($content)) {
            return '';
        }
        if (mb_strlen($content) > $maxLenght) {
            $excerpt = mb_substr($content, 0, $maxLenght);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace).'...';
        }
        return $content;
    }
}
