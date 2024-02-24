<?php

namespace App\Models;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'winner',
        'finished_at'
    ];

    protected $casts = [
        'id' => 'int',
        'status' => 'array',
        'winner' => 'string',
    ];

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
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
