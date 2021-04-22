<?php

use Controller\ProductController;

require "../../vendor/autoload.php";

$sku = (isset($_GET['sku']) && !empty(trim($_GET['sku']))) ? $_GET['sku'] : null;
$productController = new ProductController();
$productController->delete($sku);
