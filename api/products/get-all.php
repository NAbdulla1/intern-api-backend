<?php

use Controller\ProductController;

require "../../vendor/autoload.php";

(new ProductController())->getAll();