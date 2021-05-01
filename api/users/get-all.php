<?php

use Controller\UserController;
use JSON_Web_Token\JWTController;
use Utils\OtherResponse;
use Utils\ResponseCodes;

require "../../vendor/autoload.php";

\Utils\CORSHeaders::setProperResponseHeadersForCors();

$access_token = isset(apache_request_headers()['access_token']) ? apache_request_headers()['access_token'] : "";
$user = JWTController::validateToken($access_token);
if (!$user) exit();
if (!$user->isAdmin()) {
    OtherResponse::send(ResponseCodes::HTTP_FORBIDDEN, "Only Admins are allowed to list users");
    exit();
}
$page = empty($_GET['page']) ? '1' : $_GET['page'];
$pageSize = empty($_GET['page_size']) ? '3' : $_GET['page_size'];

(new UserController())->getAll($page, $pageSize);