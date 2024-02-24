<?php

namespace App\Http\Controllers;

use App\Services\Commands\NewGame\NewGameCommandHandler;
use App\Services\Commands\NewGame\NewGameResult;
use Illuminate\Http\Response;

class NewGameController extends Controller
{
    public function __construct(
        private readonly NewGameCommandHandler $commandHandler,
    )
    {
    }

    public function __invoke(): Response
    {
        $result = $this->commandHandler->handle();

        return new Response(
            $this->buildResponse($result)
        );
    }

    private function buildResponse(NewGameResult $result): array
    {
        return [
            'gameId' => $result->id,
        ];
    }
}
