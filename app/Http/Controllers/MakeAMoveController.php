<?php

namespace App\Http\Controllers;

use App\Http\Requests\MakeAMoveRequest;
use App\Services\Commands\MakeAMove\MakeAMoveCommand;
use App\Services\Commands\MakeAMove\MakeAMoveCommandHandler;
use App\Services\Commands\MakeAMove\MakeAMoveResult;
use Illuminate\Http\Response;

class MakeAMoveController extends Controller
{
    public function __construct(
        private readonly MakeAMoveCommandHandler $commandHandler,
    )
    {
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(MakeAMoveRequest $request): Response
    {
        $command = new MakeAMoveCommand(
            $request->get('gameId'),
            $request->get('player'),
            $request->get('position'),
        );

        $result = $this->commandHandler->handle($command);

        return new Response(
            $this->buildResponse($result)
        );
    }

    /**
     * @param MakeAMoveResult $result
     * @return array<string, mixed>
     */
    private function buildResponse(MakeAMoveResult $result): array
    {
        return [
            'status' => $result->status,
            'winner' => $result->winner,
            'finished' => $result->finished,
        ];
    }
}
