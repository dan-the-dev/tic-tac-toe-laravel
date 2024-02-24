<?php

namespace App\Services\Commands\NewGame;

interface NewGameCommandHandler
{
    public function handle(): NewGameResult;
}
