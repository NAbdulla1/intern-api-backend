<?php

use Controller\UserController;

require "../../vendor/autoload.php";

$content = json_decode(file_get_contents("php://input"), true);
$content = empty($content) ? null : $content;
(new UserController())->create($content);