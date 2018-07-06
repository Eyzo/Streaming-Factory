<?php
use App\Blog\BlogWidget;

return [
'blog.prefix'=>'/blog',
'admin.widgets'=>\DI\add([
    \DI\get(BlogWidget::class)
    ])


];
