<?php

namespace App\Controllers;

use App\Core\CSRF;
use App\Filters\PlayerCastFields;
use App\Validators\PlayerValidator;
use App\Models\PlayersTable;
use App\Views\AbstractView;
use App\Views\JsonView;
use App\Views\LeaderboardView;

class LeaderboardController implements ControllerInterface
{
    public function indexAction(): AbstractView
    {
        $view = new LeaderboardView();

        // Todo: redo this crap!
        $players = (new PlayersTable())->getLeaders(3);
        $view->players = $players;

        return $view;
    }

    public function createAction(): AbstractView
    {
        $requestJson = file_get_contents('php://input');
        $request = json_decode($requestJson, true);

        // With more time this type of checks should be done in middleware piplines
        $token = htmlspecialchars($request['csrf']);

        if ((new CSRF())->handleCSRFToken($token)) {
            $this->returnMethodNotAllowed();
        }

        // This functionality should probably be part of a Model as an attribute array with allowed fields. But yeah yeah.
        $castFields = (new PlayerCastFields())->cast($request);

        $validated = new PlayerValidator();
        $validated->validateRegister($castFields);

        /** In a normal case we would have set formdata in a session and returned a form with errors matching the fields
         * But right now we are working mostly with hidden data, and if that data has been modified we return and let fron-end reload page.
         */
        if ($validated->hasErrors()) {
            $this->returnMethodNotAllowed();
        }

        $playersTable = new PlayersTable();
        $playersTable->addRow(
            $castFields['name'],
            (int) $castFields['grid_size'],
            (int) $castFields['play_time'],
            date('Y-m-d H:i:s')
        );

        $json = new JsonView();
        $json->data = ['grid_size' => $castFields['grid_size']]; // ToDo: Grid_size used as param for leaderboard when we get there
        return $json;
    }

    /** Returning status like this allows us let front-end decide what to do, in our case reload page */
    private function returnMethodNotAllowed(): AbstractView
    {
        $json = new JsonView();
        $json->data = ['status' => 405, 'message' => 'method not allowed'];

        return $json;
    }
}
