<?php

use Controller\ProductController;
use JSON_Web_Token\JWTController;

require "../../vendor/autoload.php";

\Utils\CORSHeaders::setProperResponseHeadersForCors();

$access_token = isset(apache_request_headers()['access_token']) ? apache_request_headers()['access_token'] : "";
$user = JWTController::validateToken($access_token);
if (!$user) exit();

$sku = (isset($_GET['sku']) && !empty(trim($_GET['sku']))) ? $_GET['sku'] : null;
$productController = new ProductController();
$productController->getOne($sku);
