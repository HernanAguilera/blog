<?php

declare(strict_types=1);

namespace App\Providers;

use Blog\Application\Services\Auth\JwtServiceInterface;
use Blog\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function register(): void
    {
        // Register User Repository Interface
        $this->app->bind(
            UserRepositoryInterface::class,
            \Blog\Infrastructure\Persistence\Eloquent\EloquentUserRepository::class
        );

        // Register JWT Service Interface
        $this->app->bind(
            JwtServiceInterface::class,
            \Blog\Infrastructure\Services\Auth\LaravelJwtService::class
        );
    }

    /**
     * Bootstrap any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}