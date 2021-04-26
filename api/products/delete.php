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
    OtherResponse::send(ResponseCodes::HTTP_FORBIDDEN, "Only Admins are allowed to delete products");
    exit();
}

$sku = (isset($_GET['sku']) && !empty(trim($_GET['sku']))) ? $_GET['sku'] : null;
$productController = new ProductController();
$productController->delete($sku);
