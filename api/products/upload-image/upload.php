<?php
require "../../../vendor/autoload.php";

use Controller\ImageController;
use JSON_Web_Token\JWTController;
use Utils\OtherResponse;
use Utils\ResponseCodes;

header("Content-type: application/json");

$access_token = isset(apache_request_headers()['access_token']) ? apache_request_headers()['access_token'] : "";
$user = JWTController::validateToken($access_token);
if (!$user) exit();
if (!$user->isAdmin()) {
    OtherResponse::send(ResponseCodes::HTTP_FORBIDDEN, "Only Admins are allowed to upload images");
    exit();
}

$productImage = $_FILES['product_image'];
(new ImageController())->upload($productImage);