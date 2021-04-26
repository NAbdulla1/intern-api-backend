<?php

use Controller\ProductController;
use JSON_Web_Token\JWTController;
use Utils\OtherResponse;
use Utils\ResponseCodes;

require "../../vendor/autoload.php";

header("Content-type: application/json");

$access_token = isset(apache_request_headers()['access_token']) ? apache_request_headers()['access_token'] : "";
$user = JWTController::validateToken($access_token);
if (!$user) exit();
if (!$user->isAdmin()) {
    OtherResponse::send(ResponseCodes::HTTP_FORBIDDEN, "Only Admins are allowed to update products");
    exit();
}

$content = json_decode(file_get_contents("php://input"), true);
$content = empty($content) ? null : $content;

(new ProductController())->patch($content);