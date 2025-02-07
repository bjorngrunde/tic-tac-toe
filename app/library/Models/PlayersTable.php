<?php

namespace App\Models;

use App\Filters\CastableInterface;

class PlayersTable extends AbstractTable
{
    protected function getTableName(): string
    {
        return 'players';
    }

    public function getLeaders(int $gridSize): array
    {
        return $this->executeSql(
            "
                SELECT
                    DISTINCT name,
                    play_time_seconds,
                    grid_size
                FROM players
                WHERE
                    grid_size = :grid_size
                ORDER BY play_time_seconds ASC
                LIMIT 20
            ",
            [
                ':grid_size' => $gridSize,
            ]
        );
    }

    public function addRow(CastableInterface $userData, string $date): void
    {
        $this->executeSql(
            "
                INSERT INTO players
                    (name, grid_size, play_time_seconds, ctime)
                VALUE
                    (:name, :grid_size, :play_time_seconds, :date)
            ",
            [
                ':name' => $userData->getField('name'),
                ':grid_size' => $userData->getField('grid_size'),
                ':play_time_seconds' => $userData->getField('play_time'),
                ':date' => $date,
            ]
        );
    }
}
