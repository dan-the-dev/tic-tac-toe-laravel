<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class NewGameController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): Response
    {
        return new Response(
            [
                'gameId' => 1,
            ]
        );
    }
}
