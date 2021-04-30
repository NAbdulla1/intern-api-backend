<?php


namespace Repository;


use Database\DB;
use InvalidArgumentException;
use Models\Order;

class OrderRepository
{
    private DB $database;
    private array $statuses;
    private int $pageSize = 10;

    public function __construct()
    {
        $this->database = DB::instance();
        $this->statuses = (new OrderStatusRepository())->getAllStatus();
    }

    public function get($user_email, string $page): array
    {
        $orders = [];
        list($rows, $stmt) = $this->buildAndQuery($page, $user_email);
        if (!$stmt) return [$orders, $rows];
        $result_set = $stmt->get_result();
        while ($result_set && $order = $result_set->fetch_assoc())
            array_push($orders, Order::fromAssocArray($order)->toAssocArray());
        return [$orders, $rows === null ? count($orders) : $rows];
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

    public function buildAndQuery(string $page, $user_email): array
    {
        $query = "SELECT * FROM orders";
        $res = $this->database->getConnection()->query($query . (empty($user_email) ? "" : " WHERE user_email = '$user_email'"));//using user email directly because we are not inputting it from user. we are setting correct user email, so no sql-injection
        $rows = $res ? $res->num_rows : null;

        $offset = max(0, $page - 1) * $this->pageSize;
        $query .= " LIMIT ? OFFSET ?";
        $stmt = $this->database->executePreparedStatement($query, "ii", [$this->pageSize, $offset]);
        return array($rows, $stmt);
    }
}