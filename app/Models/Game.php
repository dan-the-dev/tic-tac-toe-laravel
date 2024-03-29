<?php

namespace App\Models;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property array<int, int|string> $status
 * @property int $moves
 * @property ?string $last_move
 * @property ?string $winner
 * @property ?DateTimeImmutable $finished_at
 */
class Game extends Model
{
    use HasFactory;

    const BOARD_START = [0, 1, 2, 3, 4, 5, 6, 7, 8];
    const ROWS = [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8]
    ];
    const COLUMNS = [ [0, 3, 6], [1, 4, 7], [2, 5, 8] ];
    const DIAGONALS = [ [0, 4, 8], [2, 4, 6] ];

    protected $fillable = [
        'id',
        'status',
        'moves',
        'last_move',
        'winner',
        'finished_at'
    ];

    protected $casts = [
        'id' => 'int',
        'status' => 'string',
        'winner' => 'string',
    ];

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => explode('|', $value),
            set: fn (array $value) => implode('|', $value),
        );
    }

    protected function finishedAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => is_null($value) ? null : DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, $value),
            set: fn (?DateTimeImmutable $value) => is_null($value) ? null : $value->format(DateTimeImmutable::ATOM),
        );
    }
}
