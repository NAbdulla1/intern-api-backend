<?php

use Controller\OrderController;
use JSON_Web_Token\JWTController;

require "../../vendor/autoload.php";

\Utils\CORSHeaders::setProperResponseHeadersForCors();

$access_token = isset(apache_request_headers()['access_token']) ? apache_request_headers()['access_token'] : "";
$user = JWTController::validateToken($access_token);
if (!$user) exit();

$content = json_decode(file_get_contents("php://input"), true);
$content = (empty($content) || empty($content['product_sku'])) ? null : $content;
if ($content != null) $content['user_email'] = $user->getEmail();

(new OrderController())->create($content);