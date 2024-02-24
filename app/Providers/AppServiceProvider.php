<?php

namespace App\Providers;

use App\Services\Commands\NewGame\NewGameCommandHandler;
use App\Services\Commands\NewGame\NewGameCommandHandlerDefault;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NewGameCommandHandler::class, NewGameCommandHandlerDefault::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
