<?php


namespace Repository;


use Database\DB;

class OrderStatusRepository
{
    private DB $database;

    public function __construct()
    {
        $this->database = DB::instance();
    }

    public function getAllStatus(): array
    {
        $result = $this->database->getConnection()->query("SELECT * FROM order_status");
        if (!$result) return [];
        else {
            $status = [];
            while ($row = $result->fetch_assoc()) {
                $status[(int)$row['id']] = $row['status'];
            }
            return $status;
        }
    }

}