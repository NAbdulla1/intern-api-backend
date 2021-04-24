<?php

use Controller\OrderController;
use JSON_Web_Token\JWTController;
use Utils\ResponseCodes;

require "../../vendor/autoload.php";

header("Content-type: application/json");

$access_token = isset(apache_request_headers()['access_token']) ? apache_request_headers()['access_token'] : "";
$user = JWTController::validateToken($access_token);
if (!$user) exit();
if (!$user->isAdmin()) {
    http_response_code(ResponseCodes::HTTP_FORBIDDEN);
    echo json_encode(["message" => "Only Admins are allowed to update orders"]);
    exit();
}

$content = json_decode(file_get_contents("php://input"), true);
$content = empty($content) ? null : $content;

(new OrderController())->patch($content);