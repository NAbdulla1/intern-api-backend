<?php

namespace Controller;

require "../../vendor/autoload.php";

use Exception;
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
        $productsAsAssocArray = $this->productRepository->get();
        echo json_encode(["products" => $productsAsAssocArray]);
    }

    public function getOne($sku)
    {
        if ($this->isSkuNull($sku)) return;
        $product = $this->productRepository->getOne($sku);
        if ($product) echo json_encode($product);
        else {
            http_response_code(ResponseCodes::HTTP_NOT_FOUND);
            echo json_encode(["message" => "Product Not Found"]);
        }
    }

    public function delete($sku)
    {
        if ($this->isSkuNull($sku)) return;
        $status = $this->productRepository->delete($sku);
        if ($status) http_response_code(ResponseCodes::HTTP_NO_CONTENT);
        else {
            http_response_code(ResponseCodes::HTTP_NOT_FOUND);
            echo json_encode(["message" => "Product Not Found"]);
        }
    }

    public function create($prodAssocArray)
    {
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
            echo json_encode(["message" => $prodAssocArray == null ? "No Data" : $ex->getMessage()]);
        }
    }

    public function patch($prodAssocArray)
    {
        if ($this->isInconsistentData($prodAssocArray)) return;
        if ($this->productNotExists($prodAssocArray)) return;
        if ($this->productRepository->update($prodAssocArray)) $this->getOne($prodAssocArray['sku']);
        else {
            http_response_code(ResponseCodes::HTTP_BAD_REQUEST);
            echo json_encode(["message" => "Nothing to update"]);
        }
    }

    private function isInconsistentData($inputData): bool
    {
        if ($inputData == null || !isset($inputData['sku']) || (isset($inputData['sku']) && count($inputData) < 2)) {
            http_response_code(ResponseCodes::HTTP_BAD_REQUEST);
            echo json_encode(["message" => "Inconsistent Data Provided"]);
            return true;
        }
        return false;
    }

    private function productNotExists($prodAssocArray): bool
    {
        if ($this->productRepository->getOne($prodAssocArray['sku']) == null) {
            http_response_code(ResponseCodes::HTTP_NOT_FOUND);
            echo json_encode(["message" => "Product Not Found"]);
            return true;
        }
        return false;
    }

    private function isSkuNull($sku): bool
    {
        if ($sku == null) {
            http_response_code(ResponseCodes::HTTP_BAD_REQUEST);
            echo json_encode(["message" => "Inconsistent Data Provided"]);
            return true;
        }
        return false;
    }
}