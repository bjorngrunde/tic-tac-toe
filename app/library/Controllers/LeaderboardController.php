<?php

namespace App\Controllers;

use App\Core\CSRF;
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
        $players = (new PlayersTable())->getLeaders(10);
        $view->players = $players;

        return $view;
    }

    public function createAction(): JsonView
    {
        $requestJson = file_get_contents('php://input');
        $request = json_decode($requestJson, true);

        // With more time this type of checks should be done in middleware piplines
        if (!CSRF::handleCSRFToken($request['csrf'])) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
            exit;
        }


        /**
         * 1. Check csrf token
         * 2. Create a validation class and validate data
         * 3. If validated, save data to db
         * 4. 
         */
        $json = new JsonView();
        $json->data = ['status' => http_response_code(200)];
        return $json;
    }
}
