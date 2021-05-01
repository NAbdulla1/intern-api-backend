<?php

namespace Controller;

require "../../vendor/autoload.php";

use Exception;
use Models\Product;
use MyLogger\Log;
use Repository\ProductRepository;
use Utils\OtherResponse;
use Utils\ResponseCodes;

class ProductController
{
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
    }

    public function getAll($parameters)
    {
        [$productsAsAssocArray, $productCount] = $this->productRepository->get($parameters);
        echo json_encode(["products" => $productsAsAssocArray, "count" => $productCount]);
    }

    public function getOne($sku)
    {
        if ($this->isSkuNull($sku)) return;
        $product = $this->productRepository->getOne($sku);
        if ($product) echo json_encode($product);
        else OtherResponse::send(ResponseCodes::HTTP_NOT_FOUND, "Product Not Found");
    }

    public function delete($sku)
    {
        if ($this->isSkuNull($sku)) return;
        if ($this->productRepository->getOne($sku) == null) OtherResponse::send(ResponseCodes::HTTP_NOT_FOUND, "Product Not Found");
        $status = $this->productRepository->delete($sku);
        if ($status) OtherResponse::send(ResponseCodes::HTTP_NO_CONTENT, "successfully deleted");
        else OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, "Delete Failed! There are orders associated to this product");
    }

    public function create($prodAssocArray)
    {
        try {
            $prod = Product::fromAssocArray($prodAssocArray);
            if ($this->productRepository->create($prod)) {
                http_response_code(ResponseCodes::HTTP_CREATED);
                echo json_encode($prodAssocArray);
            } else
                OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, "Product already exists");
        } catch (Exception $ex) {
            OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, $prodAssocArray == null ? "No Data" : $ex->getMessage());
        }
    }

    public function patch($prodAssocArray)
    {
        if ($this->isInconsistentData($prodAssocArray)) return;
        if ($this->productNotExists($prodAssocArray)) return;
        if ($this->productRepository->update($prodAssocArray)) $this->getOne($prodAssocArray['sku']);
        else OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, "Nothing to update");
    }

    private function isInconsistentData($inputData): bool
    {
        if ($inputData == null || !isset($inputData['sku']) || (isset($inputData['sku']) && count($inputData) < 2)) {
            OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, "Inconsistent Data Provided");
            return true;
        }
        return false;
    }

    private function productNotExists($prodAssocArray): bool
    {
        if ($this->productRepository->getOne($prodAssocArray['sku']) == null) {
            OtherResponse::send(ResponseCodes::HTTP_NOT_FOUND, "Product Not Found");
            return true;
        }
        return false;
    }

    private function isSkuNull($sku): bool
    {
        if ($sku == null) {
            OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, "Inconsistent Data Provided");
            return true;
        }
        return false;
    }

    public function getAvailableCategories($pattern)
    {
        echo json_encode($this->productRepository->getDistinctProductCategories($pattern));
    }
}