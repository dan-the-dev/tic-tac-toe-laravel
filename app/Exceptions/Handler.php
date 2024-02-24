<?php

namespace App\Exceptions;

use App\Services\Commands\MakeAMove\PlayerCantMoveTwiceException;
use App\Services\Commands\MakeAMove\PositionAlreadyTakenException;
use App\Services\Commands\NewGame\GameNotCreatedException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        return response()
            ->json([
                'message' => $e->getMessage() === '' ? 'Something went wrong!' : $e->getMessage(),
                'exception' => get_class($e)
            ]);
    }

    protected function shouldReturnJson($request, Throwable $e): bool
    {
        return parent::shouldReturnJson($request, $e) || $request->is("api/*");
    }

}

