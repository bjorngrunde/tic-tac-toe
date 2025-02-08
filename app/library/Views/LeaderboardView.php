<?php

namespace App\Views;

class LeaderboardView extends AbstractView
{
    /** @var array[][] */
    public array $players;
    public int $playerCount;
    public int $grid_size;
    public int $rank = 0;

    public function __construct()
    {
        $this->setTitle("Leaderboard");
    }

    public function render(): void
    {
        include __DIR__ . '/leaderboard.phtml';
    }

    public function showSeconds(int $milliSeconds): float
    {
        return floor($milliSeconds / 1000);
    }
}
