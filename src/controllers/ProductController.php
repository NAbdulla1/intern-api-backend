<?php

namespace Controller;

require "../../vendor/autoload.php";

use Exception;
use InvalidArgumentException;
use Models\Product;
use Repository\ProductRepository;
use Utils\ResponseCodes;

class ProductController
{
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
    }

    public function getAll()
    {
        header("Content-type: application/json");
        $productsAsAssocArray = $this->productRepository->get();
        echo json_encode(["products" => $productsAsAssocArray]);
    }

    public function getOne($sku)
    {
        header("Content-type: application/json");
        if ($sku == null) {
            http_response_code(ResponseCodes::HTTP_BAD_REQUEST);
            return;//todo what message should be passed if there is error
        }
        $product = $this->productRepository->getOne($sku);
        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(ResponseCodes::HTTP_NOT_FOUND);
            return;//todo what message should be passed if there is error
        }
    }

    public function delete($sku)
    {
        header("Content-type: application/json");
        if ($sku == null) {
            http_response_code(ResponseCodes::HTTP_BAD_REQUEST);
            return;//todo what message should be passed if there is error
        }
        $status = $this->productRepository->delete($sku);
        if ($status) {
            http_response_code(ResponseCodes::HTTP_NO_CONTENT);
        } else {
            http_response_code(ResponseCodes::HTTP_NOT_FOUND);
            return;//todo what message should be passed if there is error
        }
    }

    public function create($prodAssocArray)
    {
        header("Content-type: application/json");
        try {
            $prod = Product::fromAssocArray($prodAssocArray);
            if ($this->productRepository->create($prod)) {
                http_response_code(ResponseCodes::HTTP_CREATED);
                echo json_encode($prodAssocArray);
            } else {
                http_response_code(ResponseCodes::HTTP_BAD_REQUEST);
                echo json_encode(["message" => "Product already exists"]);
            }
        } catch (Exception $ex) {
            http_response_code(ResponseCodes::HTTP_BAD_REQUEST);
            echo json_encode(["message" => $ex->getMessage()]);
        }
    }
}