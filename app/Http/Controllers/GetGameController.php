<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetGameRequest;
use App\Services\Query\GetGame\GetGameQuery;
use App\Services\Query\GetGame\GetGameQueryHandler;
use App\Services\Query\GetGame\GetGameResult;
use Illuminate\Http\Response;

class GetGameController extends Controller
{
    public function __construct(
        private readonly GetGameQueryHandler $getGameQueryHandler
    )
    {
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(GetGameRequest $request)
    {
        $getGameQuery = new GetGameQuery(gameId: $request->get('gameId'));
        $result = $this->getGameQueryHandler->handle(query: $getGameQuery);

        return new Response(
            $this->buildResponse($result)
        );
    }

    private function buildResponse(GetGameResult $result): array
    {
        return [
            'status' => $result->status,
            'winner' => $result->winner,
            'finished' => $result->finished,
        ];
    }
}
