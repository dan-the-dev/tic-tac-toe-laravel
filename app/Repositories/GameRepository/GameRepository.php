<?php

namespace App\Repositories\GameRepository;

use App\Models\Game;

interface GameRepository
{
    public function create(): int;
}
