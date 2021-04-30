<?php

use Controller\OrderController;
use JSON_Web_Token\JWTController;

require "../../vendor/autoload.php";

\Utils\CORSHeaders::setProperResponseHeadersForCors();

$access_token = isset(apache_request_headers()['access_token']) ? apache_request_headers()['access_token'] : "";
$user = JWTController::validateToken($access_token);
if (!$user) exit();
$page = empty($_GET['page']) ? '1' : $_GET['page'];

(new OrderController())->getAll($user->isAdmin() ? null : $user->getEmail(), $page);