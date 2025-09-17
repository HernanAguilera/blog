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
use App\src\Application\UseCases\Post\AutoSavePostUseCase;
use App\src\Application\UseCases\Post\PreviewPostUseCase;
use App\src\Application\UseCases\Post\ChangePostStatusUseCase;
use App\src\Domain\Post\Services\HtmlSanitizerInterface;
use App\src\Infrastructure\Services\HtmlSanitizerService;
use App\src\Interface\Console\Commands\CleanupDraftsCommand;
use App\src\Interface\Console\Commands\CleanupPreviewsCommand;
use App\src\Domain\Shared\Events\EventDispatcherInterface;
use Illuminate\Support\ServiceProvider;

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

        // Service bindings
        $this->app->bind(
            HtmlSanitizerInterface::class,
            HtmlSanitizerService::class
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

        // Editor Use Cases bindings
        $this->app->bind(AutoSavePostUseCase::class, function ($app) {
            return new AutoSavePostUseCase(
                $app->make(HtmlSanitizerInterface::class)
            );
        });

        $this->app->bind(PreviewPostUseCase::class, function ($app) {
            return new PreviewPostUseCase(
                $app->make(HtmlSanitizerInterface::class)
            );
        });

        $this->app->bind(ChangePostStatusUseCase::class, function ($app) {
            return new ChangePostStatusUseCase(
                $app->make(PostRepositoryInterface::class),
                $app->make(EventDispatcherInterface::class)
            );
        });

        // Register Event Dispatcher - convert Laravel dispatcher to Domain interface
        $this->app->bind(EventDispatcherInterface::class, function () {
            // Create a simple adapter that implements Domain EventDispatcherInterface
            return new class(app('events')) implements EventDispatcherInterface {
                public function __construct(private $dispatcher) {}

                public function dispatch(object $event): void {
                    $this->dispatcher->dispatch($event);
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
        // Register Artisan commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanupDraftsCommand::class,
                CleanupPreviewsCommand::class,
            ]);
        }
    }
}
