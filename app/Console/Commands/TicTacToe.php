<?php

namespace App\Console\Commands;

use App\Services\Commands\MakeAMove\MakeAMoveCommand;
use App\Services\Commands\MakeAMove\MakeAMoveCommandHandler;
use App\Services\Commands\NewGame\NewGameCommandHandler;
use Illuminate\Console\Command;

class TicTacToe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tictactoe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Play a Tic Tac Toe game';

    public function __construct(
        private NewGameCommandHandler $newGameCommandHandler,
        private MakeAMoveCommandHandler $makeAMoveCommandHandler,
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->header('-------------------------------------------');
        $this->header('| Welcome in Tic Tac Toe from Dan the Dev |');
        $this->header('-------------------------------------------');

        $newGame = $this->newGameCommandHandler->handle();

        $this->header("New game (#{$newGame->id}):");
        $this->paragraph('');
        $this->paragraph(" {$this->printPosition(0)} | {$this->printPosition(1)} | {$this->printPosition(2)} ");
        $this->paragraph('-----------');
        $this->paragraph(" {$this->printPosition(3)} | {$this->printPosition(4)} | {$this->printPosition(5)} ");
        $this->paragraph('-----------');
        $this->paragraph(" {$this->printPosition(6)} | {$this->printPosition(7)} | {$this->printPosition(8)} ");

        $player = 'X';
        $finished = false;
        while (!$finished) {
            $position = 0;
            $position = $this->askMove($position, $player);

            $command = new MakeAMoveCommand(
                gameId: $newGame->id,
                player: $player,
                position: $position-1,
            );
            $makeAMoveResult = $this->makeAMoveCommandHandler->handle($command);

            $this->header("Game #{$newGame->id} - current status:");
            $this->paragraph('');
            $this->paragraph(" {$this->printPosition($makeAMoveResult->status[0])} | {$this->printPosition($makeAMoveResult->status[1])} | {$this->printPosition($makeAMoveResult->status[2])} ");
            $this->paragraph('-----------');
            $this->paragraph(" {$this->printPosition($makeAMoveResult->status[3])} | {$this->printPosition($makeAMoveResult->status[4])} | {$this->printPosition($makeAMoveResult->status[5])} ");
            $this->paragraph('-----------');
            $this->paragraph(" {$this->printPosition($makeAMoveResult->status[6])} | {$this->printPosition($makeAMoveResult->status[7])} | {$this->printPosition($makeAMoveResult->status[8])} ");

            if ($makeAMoveResult->finished) {
                $this->header('');
                $this->header('| !!! Game finished !!! |');
                $this->header('');

                if (is_null($makeAMoveResult->winner)) {
                    $this->normalAnnouncement('----------------');
                    $this->normalAnnouncement('| No one wins! |');
                    $this->normalAnnouncement('----------------');
                } else {
                    $this->happyAnnouncement('--------------------------------');
                    $this->happyAnnouncement("| Congratulations {$makeAMoveResult->winner}, you won!! |");
                    $this->happyAnnouncement('--------------------------------');

                    $this->paragraph(" {$this->printWinningPosition($makeAMoveResult->status[0], 0, $makeAMoveResult->setWinner)} | {$this->printWinningPosition($makeAMoveResult->status[1], 1, $makeAMoveResult->setWinner)} | {$this->printWinningPosition($makeAMoveResult->status[2], 2, $makeAMoveResult->setWinner)} ");
                    $this->paragraph('-----------');
                    $this->paragraph(" {$this->printWinningPosition($makeAMoveResult->status[3], 3, $makeAMoveResult->setWinner)} | {$this->printWinningPosition($makeAMoveResult->status[4], 4, $makeAMoveResult->setWinner)} | {$this->printWinningPosition($makeAMoveResult->status[5], 5, $makeAMoveResult->setWinner)} ");
                    $this->paragraph('-----------');
                    $this->paragraph(" {$this->printWinningPosition($makeAMoveResult->status[6], 6, $makeAMoveResult->setWinner)} | {$this->printWinningPosition($makeAMoveResult->status[7], 7, $makeAMoveResult->setWinner)} | {$this->printWinningPosition($makeAMoveResult->status[8], 8, $makeAMoveResult->setWinner)} ");
                }
            }

            $player = ($player === 'X') ? 'Y' : 'X';
            $finished = $makeAMoveResult->finished;
        }

    }

    private function header(string $message): void
    {
        $this->line("<fg=magenta>$message</>");
    }

    private function paragraph(string $message): void
    {
        $this->line("<fg=white>$message</>");
    }

    private function normalAnnouncement(string $message): void
    {
        $this->line("<fg=black;bg=yellow>$message</>");
    }

    private function happyAnnouncement(string $message): void
    {
        $this->line("<fg=black;bg=green>$message</>");
    }

    private function askMove(mixed $position, string $player): mixed
    {
        while ($position < 1 || $position > 9) {
            $position = $this->ask("Player $player, make the first move - pick a position between 1 and 9:");
        }
        return $position;
    }

    private function printPosition(string $position): string
    {
        if ($position === 'X') {
            return "<fg=yellow>$position</>";
        }
        if ($position === 'Y') {
            return "<fg=red>$position</>";
        }

        $positionOffsetForPrint = (int)$position + 1;
        return "<fg=cyan>$positionOffsetForPrint</>";
    }

    private function printWinningPosition(string $text, int $position, array $winningSet): string
    {
        if (in_array($position, $winningSet)) {
            return "<fg=green>$text</>";
        }
        return "<fg=white>$text</>";
    }
}
