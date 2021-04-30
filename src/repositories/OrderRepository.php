<?php


namespace Repository;


use Database\DB;
use InvalidArgumentException;
use Models\Order;

class OrderRepository
{
    private DB $database;
    private array $statuses;

    public function __construct()
    {
        $this->database = DB::instance();
        $this->statuses = (new OrderStatusRepository())->getAllStatus();
    }

    public function get($user_email): array
    {
        $orders = [];
        $query = "SELECT * FROM orders";
        if ($user_email) $query .= " WHERE user_email = '$user_email'";
        $result_set = $this->database->getConnection()->query($query);
        while ($result_set && $order = $result_set->fetch_assoc())
            array_push($orders, Order::fromAssocArray($order)->toAssocArray());
        return $orders;
    }

    /**
     * @param Order $order
     * @return false|Order returns newly created order or false if there is an error
     */
    public function create(Order $order)
    {
        $order->setStatusId(1);//an order in creating will always be in processing mode, and processing mode's id is 1
        $stmt = $this->database->executePreparedStatement("INSERT INTO orders(product_sku, user_email, status_id) VALUES (?, ?, ?)",
            "ssi", [$order->getProductSku(), $order->getUserEmail(), $order->getStatusId()]);
        if (!$stmt || $stmt->affected_rows != 1) return false;
        $orderID = $stmt->insert_id;
        return new Order($orderID, $order->getProductSku(), $order->getUserEmail(), 1);
    }

    public function getOne($id): ?array
    {
        $stmt = $this->database->executePreparedStatement("SELECT * FROM orders WHERE id = ?", "i", [(int)$id]);
        if ($stmt) {
            $result = $stmt->get_result();
            if ($result && ($order = $result->fetch_assoc())) return $order;
        }
        return null;
    }

    public function update($content): bool
    {
        $stmt = $this->database->executePreparedStatement("UPDATE orders SET status_id = ? WHERE id = ?", "ii", [$this->revStatus($content['status']), $content['id']]);
        return $stmt && $stmt->affected_rows > 0;
    }

    private function revStatus($status): int
    {
        for ($i = 1; $i <= 3; $i++) if (strcasecmp($this->statuses[$i], $status) == 0) return $i;
        throw new InvalidArgumentException("unknown order status");
    }

    public function delete($order_id): bool
    {
        $stmt = $this->database->executePreparedStatement("DELETE FROM orders WHERE id=?", "i", [(int)$order_id]);
        return $stmt && $stmt->affected_rows == 1;
    }
}