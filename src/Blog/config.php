<?php
use App\Blog\BlogWidget;

return [
'blog.prefix'=>'/streaming',
'admin.widgets'=>\DI\add([
    \DI\get(BlogWidget::class)
    ])


];
