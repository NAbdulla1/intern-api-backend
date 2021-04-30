<?php


namespace Controller;


use Exception;
use Models\Order;
use Repository\OrderRepository;
use Repository\ProductRepository;
use Utils\OtherResponse;
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

    public function getAll($user_email, string $page)
    {
        list($ordersAsAssocArray, $ordersCount) = $this->orderRepository->get($user_email, $page);
        echo json_encode(["orders" => $ordersAsAssocArray, 'count' => $ordersCount]);
    }

    public function create($orderAssocArray)
    {
        try {
            $order = Order::fromAssocArray($orderAssocArray);
            if ($order = $this->orderRepository->create($order)) {
                http_response_code(ResponseCodes::HTTP_CREATED);
                echo json_encode($order->toAssocArray());
            } else OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, $this->productRepository->getOne($orderAssocArray['product_sku']) == null ? "No product found" : "Can't create order");
        } catch (Exception $ex) {
            OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, $orderAssocArray == null ? "No Data" : $ex->getMessage());
        }
    }

    private function getOne($id)
    {
        $order = $this->orderRepository->getOne($id);
        if ($order) echo json_encode(Order::fromAssocArray($order)->toAssocArray());
        else OtherResponse::send(ResponseCodes::HTTP_NOT_FOUND, "Order Not Found");
    }

    public function patch($content)
    {
        if ($this->isInconsistentData($content)) return;
        if ($this->isOrderNotExist($content)) return;
        try {
            if ($this->orderRepository->update($content)) $this->getOne($content['id']);
            else OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, "Nothing to update");
        } catch (Exception $ex) {
            OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }

    private function isInconsistentData($inputData): bool
    {
        if ($inputData == null || empty($inputData['id']) || empty($inputData['status'])) {
            OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, "Inconsistent Data Provided");
            return true;
        }
        return false;
    }

    private function isOrderNotExist($content): bool
    {
        if ($this->orderRepository->getOne($content['id']) == null) {
            OtherResponse::send(ResponseCodes::HTTP_NOT_FOUND, "Order Not Found");
            return true;
        }
        return false;
    }

    public function delete($content)
    {
        if ($content == null || empty($content['id'])) {
            OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, "Provide an id to delete the order");
            return;
        }
        if ($this->isOrderNotExist($content)) return;
        if ($this->orderRepository->delete($content['id'])) OtherResponse::send(ResponseCodes::HTTP_NO_CONTENT, 'Order Deleted Successfully');
        else OtherResponse::send(ResponseCodes::HTTP_INTERNAL_SERVER_ERROR, "Can't delete order");
    }
}