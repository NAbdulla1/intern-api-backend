<?php

use Controller\LoginController;

require "../../vendor/autoload.php";

$credentials = json_decode(file_get_contents("php://input"), true);
$credentials = empty($credentials) ? null : $credentials;
(new LoginController())->login($credentials);