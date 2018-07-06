<?php
namespace App\Blog;

use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Module;
use Psr\Container\ContainerInterface;
use App\Blog\Action\Crud\PostCrudAction;
use App\Blog\Action\Crud\CategoryCrudAction;
use App\Blog\Action\Crud\GenreCrudAction;
use App\Blog\Action\Crud\VersionCrudAction;
use App\Blog\Action\Crud\LecteurVideoCrudAction;
use App\Blog\Action\Crud\EpisodeCrudAction;
use App\Blog\Action\Crud\ChoiceCrudAction;
use App\Blog\Action\Crud\VideoCrudAction;
use App\Blog\Action\Crud\PostGenreCrudAction;
use App\Blog\Action\PostShowAction;
use App\Blog\Action\PostIndexAction;
use App\Blog\Action\CategoryShowAction;
use App\Blog\Action\EpisodeShowAction;
use App\Blog\Action\GenreShowAction;
use App\Blog\Action\VideoShowAction;
use App\Blog\Action\AlphabetShowAction;
use App\Blog\Action\SearchShowAction;
use App\Blog\Action\AddCommentAction;
use App\Blog\Action\Crud\CommentaireCrudAction;
use App\Blog\Action\LikeAction;

class BlogModule extends Module
{
    const DEFINITIONS = __DIR__.'/config.php';
    const MIGRATIONS = __DIR__.'/db/migrations';
    const SEEDS = __DIR__.'/db/seeds';
    
    public function __construct(ContainerInterface $container)
    {
        $renderer = $container->get(RendererInterface::class);
        $renderer->addPath('Blog', __DIR__.'/views');

        $router = $container->get(Router::class);
        $router->get($container->get('blog.prefix'), PostIndexAction::class, 'blog.index');

        $router->get($container->get('blog.prefix').
        	'/oeuvre/{slug_post:[a-z0-9\-]+}', PostShowAction::class, 'blog.show');

        $router->get($container->get('blog.prefix').
        	'/category/{slug_category:[a-z0-9\-]+}', CategoryShowAction::class, 'blog.category');

        $router->get($container->get('blog.prefix').'/oeuvre/{slug_post:[a-z0-9\-]+}/episode/{slug_episode:[a-z0-9\-]+}',EpisodeShowAction::class,'blog.episode');

        $router->get($container->get('blog.prefix').'/oeuvre/{slug_post:[a-z0-9\-]+}/episode/{slug_episode:[a-z0-9\-]+}/lecteur-video/{slug_lecteur:[a-z0-9\-]+}',VideoShowAction::class,'blog.video');

        $router->get($container->get('blog.prefix').'/category/{slug_category:[a-z0-9\-]+}/genre/{slug_genre:[a-z0-9\-]+}',GenreShowAction::class,'blog.genre');

        $router->get($container->get('blog.prefix').'/category/{slug_category:[a-z0-9\-]+}/alphabet/{alphabet:[0-9A-Z]{1}}',AlphabetShowAction::class,'blog.alphabet');

        $router->post($container->get('blog.prefix').'/search',SearchShowAction::class,'blog.search.post');

        $router->get($container->get('blog.prefix').'/search/{search:[0-9a-zA-Z]+}',SearchShowAction::class,'blog.search.result');


        $router->post($container->get('blog.prefix').
            '/oeuvre/{slug_post:[a-z0-9\-]+}',AddCommentAction::class);

        $router->post($container->get('blog.prefix').'/oeuvre/{slug_post:[a-z0-9\-]+}/like/{vote:[1\-]+}',LikeAction::class,'blog.like');
        

        

        if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            /**
            *Post
            */
        $router->get($prefix.'/posts', PostCrudAction::class, 'blog.admin.index');
        $router->get($prefix.'/posts'.'/{id:[0-9]+}', PostCrudAction::class,'blog.admin.edit');
        $router->post($prefix.'/posts'.'/{id:[0-9]+}', PostCrudAction::class);
        $router->get($prefix.'/posts'.'/new', PostCrudAction::class,'blog.admin.create');
        $router->post($prefix.'/posts'.'/new', PostCrudAction::class);
        $router->delete($prefix.'/posts'.'/{id:[0-9]+}', PostCrudAction::class,'blog.admin.delete');
        $router->post($prefix.'/posts/search',PostCrudAction::class,'blog.admin.search');


            /**
            *Commentaire
            */
            
            $router->get($prefix.'/commentaire',CommentaireCrudAction::class,'blog.admin.commentaire.index');

            $router->delete($prefix.'/commentaire/{id:[0-9]+}',CommentaireCrudAction::class,'blog.admin.commentaire.delete');




            $router->crud($prefix.'/categories', CategoryCrudAction::class, 'blog.category.admin');

            $router->crud($prefix.'/genre',GenreCrudAction::class,'blog.genre.admin');

            $router->crud($prefix.'/version',VersionCrudAction::class,'blog.version.admin');

            $router->crud($prefix.'/lecteur-video',LecteurVideoCrudAction::class,'blog.lecteurvideo.admin');

            $router->get($prefix.'/posts/{id_oeuvre:[0-9]+}/episode_version',ChoiceCrudAction::class,'blog.choice.admin.index');

            $router->crud($prefix.'/posts/{id_oeuvre:[0-9]+}/episode_version/{id_version:[0-9]+}/episode',EpisodeCrudAction::class,'blog.episode.admin');

            $router->crud($prefix.'/episode/{id_episode:[0-9]+}/video',VideoCrudAction::class,'blog.video.admin');

            $router->crud($prefix.'/posts/{id_oeuvre:[0-9]+}/genre',PostGenreCrudAction::class,'blog.postgenre.admin');

             

            
        }
    }
}
