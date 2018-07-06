<?php
namespace App\Blog;

use Framework\Upload;

class PostUpload extends Upload
{

    protected $path = 'public/upload/posts';

    protected $formats = [
    'thumb'=>[320,180]
    ];
}