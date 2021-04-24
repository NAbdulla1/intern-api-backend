<?php

use Controller\OrderController;
use JSON_Web_Token\JWTController;

require "../../vendor/autoload.php";

header("Content-type: application/json");

$access_token = isset(apache_request_headers()['access_token']) ? apache_request_headers()['access_token'] : "";
$user = JWTController::validateToken($access_token);
if (!$user) exit();

(new OrderController())->getAll($user->isAdmin() ? null : $user->getEmail());