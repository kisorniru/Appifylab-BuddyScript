<?php

namespace App\Providers;

use App\Repositories\Contracts\AuthApiRepositoryInterface;
use App\Repositories\Contracts\FeedApiRepositoryInterface;
use App\Repositories\Contracts\NotificationApiRepositoryInterface;
use App\Repositories\Contracts\PostApiRepositoryInterface;
use App\Repositories\Eloquent\AuthApiRepository;
use App\Repositories\Eloquent\FeedApiRepository;
use App\Repositories\Eloquent\NotificationApiRepository;
use App\Repositories\Eloquent\PostApiRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AuthApiRepositoryInterface::class,
            AuthApiRepository::class
        );

        $this->app->bind(
            FeedApiRepositoryInterface::class,
            FeedApiRepository::class
        );

        $this->app->bind(
            PostApiRepositoryInterface::class,
            PostApiRepository::class
        );

        $this->app->bind(
            NotificationApiRepositoryInterface::class,
            NotificationApiRepository::class
        );

    }

    public function boot(): void {}
}
