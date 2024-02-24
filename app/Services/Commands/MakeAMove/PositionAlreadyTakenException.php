<?php

namespace App\Services\Commands\MakeAMove;

use Exception;

class PositionAlreadyTakenException extends Exception
{
    public function __construct(int $position)
    {
        $printablePosition = $position + 1;
        parent::__construct("Position {$printablePosition} already taken");
    }
}
