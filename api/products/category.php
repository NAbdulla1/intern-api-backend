<?php

use Controller\ProductController;
use JSON_Web_Token\JWTController;

require "../../vendor/autoload.php";

\Utils\CORSHeaders::setProperResponseHeadersForCors();

$access_token = isset(apache_request_headers()['access_token']) ? apache_request_headers()['access_token'] : "";
$user = JWTController::validateToken($access_token);
if (!$user) exit();

$pattern = empty($_GET['pattern'])?'*':$_GET['pattern'];
$productController = new ProductController();
$productController->getAvailableCategories($pattern);
