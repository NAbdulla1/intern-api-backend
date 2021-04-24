<?php


namespace Controller;


use Exception;
use Models\Order;
use Repository\OrderRepository;
use Repository\ProductRepository;
use Utils\ResponseCodes;

class OrderController
{
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->productRepository = new ProductRepository();
    }

    public function getAll($user_email)
    {
        $ordersAsAssocArray = $this->orderRepository->get($user_email);
        echo json_encode(["orders" => $ordersAsAssocArray]);
    }

    public function create($orderAssocArray)
    {
        try {
            $order = Order::fromAssocArray($orderAssocArray);
            if ($order = $this->orderRepository->create($order)) {
                http_response_code(ResponseCodes::HTTP_CREATED);
                echo json_encode($order->toAssocArray());
            } else {
                http_response_code(ResponseCodes::HTTP_BAD_REQUEST);
                echo json_encode(["message" => $this->productRepository->getOne($orderAssocArray['product_sku']) == null ? "No product found" : "Can't create order"]);
            }
        } catch (Exception $ex) {
            http_response_code(ResponseCodes::HTTP_BAD_REQUEST);
            echo json_encode(["message" => $orderAssocArray == null ? "No Data" : $ex->getMessage()]);
        }
    }

    private function getOne($id)
    {
        $order = $this->orderRepository->getOne($id);
        if ($order) echo json_encode(Order::fromAssocArray($order)->toAssocArray());
        else {
            http_response_code(ResponseCodes::HTTP_NOT_FOUND);
            echo json_encode(["message" => "Order Not Found"]);
        }
    }

    public function patch($content)
    {
        if ($this->isInconsistentData($content)) return;
        if ($this->isOrderNotExist($content)) return;
        try {
            if ($this->orderRepository->update($content)) $this->getOne($content['id']);
            else {
                http_response_code(ResponseCodes::HTTP_BAD_REQUEST);
                echo json_encode(["message" => "Nothing to update"]);
            }
        } catch (Exception $ex) {
            http_response_code(ResponseCodes::HTTP_BAD_REQUEST);
            echo json_encode(["message" => $ex->getMessage()]);
        }
    }

    private function isInconsistentData($inputData): bool
    {
        if ($inputData == null || empty($inputData['id']) || empty($inputData['status'])) {
            http_response_code(ResponseCodes::HTTP_BAD_REQUEST);
            echo json_encode(["message" => "Inconsistent Data Provided"]);
            return true;
        }
        return false;
    }

    private function isOrderNotExist($content): bool
    {
        if ($this->orderRepository->getOne($content['id']) == null) {
            http_response_code(ResponseCodes::HTTP_NOT_FOUND);
            echo json_encode(["message" => "Order Not Found"]);
            return true;
        }
        return false;
    }
}