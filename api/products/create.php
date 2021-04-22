<?php

use Controller\ProductController;

require "../../vendor/autoload.php";
header("Access-Control-Allow-Methods: POST");

$content = json_decode(file_get_contents("php://input"), true);
$content = empty($content) ? null : $content;

(new ProductController())->create($content);