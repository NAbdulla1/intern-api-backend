<?php


namespace Models;


use InvalidArgumentException;
use Repository\OrderStatusRepository;

class Order
{
    private int $id;
    private string $product_sku;
    private string $user_email;
    private int $status_id;

    public function __construct(int $id, string $product_sku, string $user_email, int $status_id)
    {
        $this->setId($id);
        $this->setProductSku($product_sku);
        $this->setUserEmail($user_email);
        $this->setStatusId($status_id);
    }

    public function getId(): int
    {
        return $this->id;
    }

    private function setId(int $id): void
    {
        if ($id < 1)
            throw new InvalidArgumentException("Order id must be > 0");
        $this->id = $id;
    }

    public function getProductSku(): string
    {
        return $this->product_sku;
    }

    private function setProductSku(string $product_sku): void
    {
        $this->checkStringConstraint("product_sku", $product_sku, 1, 20);
        $this->product_sku = $product_sku;
    }

    public function getUserEmail(): string
    {
        return $this->user_email;
    }

    private function setUserEmail(string $user_email): void
    {
        $this->checkStringConstraint("user_email", $user_email, 1, 100);
        $this->user_email = $user_email;
    }

    public function getStatusId(): int
    {
        return $this->status_id;
    }

    public function setStatusId(int $status_id): void
    {
        if ($status_id < 1 || $status_id > 3)
            throw new InvalidArgumentException("Order status must be 'processing', 'shipped' or 'delivered'");
        $this->status_id = $status_id;
    }

    private function checkStringConstraint($fieldName, $field, $min, $max)
    {
        if ($min <= strlen($field) && strlen($field) <= $max) return;
        throw new InvalidArgumentException("Order $fieldName length must be in between $min and $max");
    }

    public static function fromAssocArray($order): Order
    {
        $order = self::fillMissingValue($order);
        return new Order(
            (int)$order['id'],
            $order['product_sku'],
            $order['user_email'],
            (int)$order['status_id']
        );
    }

    private static function fillMissingValue($assocArray)
    {
        $keys = ["id", "product_sku", "user_email", "status_id"];
        foreach ($keys as $key)
            if (!isset($assocArray[$key]))
                $assocArray[$key] = ($key == "id" || $key == "status_id") ? "1" : "";
        return $assocArray;
    }

    public function toAssocArray(): array
    {
        $statuses = (new OrderStatusRepository())->getAllStatus();
        return ["id" => $this->getId(), "product_sku" => $this->getProductSku(),
            "user_email" => $this->getUserEmail(), "status" => $statuses[$this->getStatusId()]];
    }
}