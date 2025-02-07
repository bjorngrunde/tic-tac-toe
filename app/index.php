<?php

use App\Core\Mvc;

require_once('vendor/autoload.php');

session_start();

$routeProcessor = new Mvc();
$routeProcessor->processCurrentRequest();
