<?php

namespace Tests\Integration\Repositories\GameRepository;

use App\Models\Game;
use App\Repositories\GameRepository\DatabaseGameRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseGameRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private DatabaseGameRepository $repository;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->repository = new DatabaseGameRepository();
    }

    public function testItCreatesNewGameAndReturnsIdOfTheGame():  void
    {
        $this->assertNull(Game::first());

        $actual = $this->repository->create();

        $first = Game::first();
        $this->assertNotNull($first);
        $this->assertEquals($first->id, $actual);
    }
}
