<?php

namespace App\Providers;

use App\src\Domain\Post\Repositories\PostRepositoryInterface;
use App\src\Infrastructure\Persistence\Eloquent\Repositories\EloquentPostRepository;
use App\src\Application\UseCases\Post\CreatePostUseCase;
use App\src\Application\UseCases\Post\UpdatePostUseCase;
use App\src\Application\UseCases\Post\DeletePostUseCase;
use App\src\Application\UseCases\Post\GetPostUseCase;
use App\src\Application\UseCases\Post\GetPublishedPostsUseCase;
use App\src\Application\UseCases\Post\GetAllPostsUseCase;
use App\src\Application\UseCases\Post\GetDraftPostsUseCase;
use App\src\Application\UseCases\Post\GetArchivedPostsUseCase;
use Illuminate\Support\ServiceProvider;
use Psr\EventDispatcher\EventDispatcherInterface;

class BlogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Repository bindings
        $this->app->bind(
            PostRepositoryInterface::class,
            EloquentPostRepository::class
        );

        // Use Cases bindings
        $this->app->bind(CreatePostUseCase::class, function ($app) {
            return new CreatePostUseCase(
                $app->make(PostRepositoryInterface::class),
                $app->make(EventDispatcherInterface::class)
            );
        });

        $this->app->bind(UpdatePostUseCase::class, function ($app) {
            return new UpdatePostUseCase(
                $app->make(PostRepositoryInterface::class),
                $app->make(EventDispatcherInterface::class)
            );
        });

        $this->app->bind(DeletePostUseCase::class, function ($app) {
            return new DeletePostUseCase(
                $app->make(PostRepositoryInterface::class),
                $app->make(EventDispatcherInterface::class)
            );
        });

        $this->app->bind(GetPostUseCase::class, function ($app) {
            return new GetPostUseCase(
                $app->make(PostRepositoryInterface::class)
            );
        });

        $this->app->bind(GetPublishedPostsUseCase::class, function ($app) {
            return new GetPublishedPostsUseCase(
                $app->make(PostRepositoryInterface::class)
            );
        });

        $this->app->bind(GetAllPostsUseCase::class, function ($app) {
            return new GetAllPostsUseCase(
                $app->make(PostRepositoryInterface::class)
            );
        });

        $this->app->bind(GetDraftPostsUseCase::class, function ($app) {
            return new GetDraftPostsUseCase(
                $app->make(PostRepositoryInterface::class)
            );
        });

        $this->app->bind(GetArchivedPostsUseCase::class, function ($app) {
            return new GetArchivedPostsUseCase(
                $app->make(PostRepositoryInterface::class)
            );
        });

        // Register Event Dispatcher - convert Laravel dispatcher to PSR compatible
        $this->app->bind(EventDispatcherInterface::class, function () {
            // Create a simple adapter that implements PSR EventDispatcherInterface
            return new class(app('events')) implements EventDispatcherInterface {
                public function __construct(private $dispatcher) {}

                public function dispatch(object $event) {
                    return $this->dispatcher->dispatch($event);
                }
            };
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
