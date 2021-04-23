<?php

use Controller\UserController;

require "../../vendor/autoload.php";

header("Content-type: application/json");

$content = json_decode(file_get_contents("php://input"), true);
$content = empty($content) ? null : $content;
(new UserController())->create($content);