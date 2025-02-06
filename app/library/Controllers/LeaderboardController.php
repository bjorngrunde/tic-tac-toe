<?php

namespace App\Controllers;

use App\Models\PlayersTable;
use App\Views\AbstractView;
use App\Views\LeaderboardView;

class LeaderboardController implements ControllerInterface
{
    public function indexAction(): AbstractView
    {
        $view = new LeaderboardView();

        // Todo: redo this crap!
        $players = (new PlayersTable())->getLeaders(10);
        $view->players = $players;

        return $view;
    }
}
