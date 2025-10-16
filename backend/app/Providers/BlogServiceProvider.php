<?php

namespace App\Providers;

use Blog\Domain\Post\Repositories\PostRepositoryInterface;
use Blog\Domain\Comment\Repositories\CommentRepositoryInterface;
use Blog\Infrastructure\Persistence\Eloquent\Repositories\EloquentPostRepository;
use Blog\Infrastructure\Persistence\Eloquent\Repositories\EloquentCommentRepository;
use Blog\Application\UseCases\Post\CreatePostUseCase;
use Blog\Application\UseCases\Post\UpdatePostUseCase;
use Blog\Application\UseCases\Post\DeletePostUseCase;
use Blog\Application\UseCases\Post\GetPostUseCase;
use Blog\Application\UseCases\Post\GetPublishedPostsUseCase;
use Blog\Application\UseCases\Post\GetAllPostsUseCase;
use Blog\Application\UseCases\Post\GetDraftPostsUseCase;
use Blog\Application\UseCases\Post\GetArchivedPostsUseCase;
use Blog\Application\UseCases\Post\AutoSavePostUseCase;
use Blog\Application\UseCases\Post\PreviewPostUseCase;
use Blog\Application\UseCases\Post\ChangePostStatusUseCase;
use Blog\Domain\Post\Services\HtmlSanitizerInterface;
use Blog\Infrastructure\Services\HtmlSanitizerService;
use Blog\Interface\Console\Commands\CleanupDraftsCommand;
use Blog\Interface\Console\Commands\CleanupPreviewsCommand;
use Blog\Domain\Shared\Events\EventDispatcherInterface;
use Blog\Application\Services\Moderation\ModerationService;
use Blog\Domain\Comment\Services\CommentDomainService;
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

        $this->app->bind(
            CommentRepositoryInterface::class,
            EloquentCommentRepository::class
        );

        // Service bindings
        $this->app->bind(
            HtmlSanitizerInterface::class,
            HtmlSanitizerService::class
        );

        // Comment services
        $this->app->singleton(ModerationService::class, function ($app) {
            return new ModerationService(
                $app->make(CommentDomainService::class),
                config('moderation.comments', [])
            );
        });

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
