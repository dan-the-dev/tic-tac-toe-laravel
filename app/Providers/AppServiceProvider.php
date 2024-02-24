<?php

namespace App\Providers;

use App\Repositories\GameRepository\DatabaseGameRepository;
use App\Repositories\GameRepository\GameRepository;
use App\Services\Commands\MakeAMove\DefaultMakeAMoveCommandHandler;
use App\Services\Commands\MakeAMove\MakeAMoveCommandHandler;
use App\Services\Commands\NewGame\NewGameCommandHandler;
use App\Services\Commands\NewGame\DefaultNewGameCommandHandler;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NewGameCommandHandler::class, DefaultNewGameCommandHandler::class);
        $this->app->bind(MakeAMoveCommandHandler::class, DefaultMakeAMoveCommandHandler::class);
        $this->app->bind(GameRepository::class, DatabaseGameRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
