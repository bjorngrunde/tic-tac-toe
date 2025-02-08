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
        $grid_size = intval($_GET['grid_size'] ?? 3);

        if ($grid_size < 3 || $grid_size > 10) {
            $grid_size = 3;
        }

        $view = new LeaderboardView();

        $players = new PlayersTable();

        $playersData = $players->getLeaders($grid_size);
        $countData = $players->getCount(['grid_size = :grid_size'], [':grid_size' => $grid_size], distinctField: 'name');

        $view->players = $playersData;
        $view->playerCount = $countData;
        $view->grid_size = $grid_size;

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
        $castFields = new PlayerCastFields();
        $castFields->cast($request);

        $validated = new PlayerValidator();
        $validated->validateRegister($castFields->getFields());

        /** In a normal case we would have set formdata in a session and returned a form with errors matching the fields
         * But right now we are working mostly with hidden data, and if that data has been modified we return and let front-end reload page.
         */
        if ($validated->hasErrors()) {
            $this->returnMethodNotAllowed();
        }

        $playersTable = new PlayersTable();
        $playersTable->addRow(userData: $castFields, date: date('Y-m-d H:i:s'));

        $json = new JsonView();
        $json->data = ['grid_size' => $castFields->getField('grid_size')];
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
