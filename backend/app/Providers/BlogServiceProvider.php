<?php

namespace App\Providers;

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
        // Aquí se enlazarán las interfaces con sus implementaciones concretas.
        // Ejemplo:
        // $this->app->bind(
        //     \Blog\Domain\Repositories\UserRepositoryInterface::class,
        //     \Blog\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository::class
        // );
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
