<?php


namespace Repository;


use Database\DB;
use Models\Product;

class ProductRepository
{
    private DB $database;

    public function __construct()
    {
        $this->database = DB::instance();
    }

    public function get(): array
    {
        $products = [];
        $query = "SELECT * FROM products";
        $result_set = $this->database->getConnection()->query($query);
        while ($prod = $result_set->fetch_assoc())
            array_push($products, $prod);
        return $products;
    }

    public function getOne($sku): ?array
    {
        $stmt = $this->database->executePreparedStatement("SELECT * FROM products WHERE sku = ?", "s", [$sku]);
        if (!$stmt) return null;
        $result_set = $stmt->get_result();
        if ($prod = $result_set->fetch_assoc())
            return $prod;
        return null;
    }

    public function delete($sku)
    {
        $stmt = $this->database->executePreparedStatement("DELETE FROM products WHERE sku = ?", "s", [$sku]);
        return $stmt->affected_rows > 0;
    }

    public function create(Product $product)
    {
        $stmt = $this->database->executePreparedStatement("INSERT INTO products(name, sku, description, category, price, imageUrl) VALUES (?, ?, ?, ?, ?, ?)",
            "ssssds", [$product->getName(), $product->getSku(), $product->getDescription(), $product->getCategory(), $product->getPrice(), $product->getImageUrl()]);
        return $stmt && $stmt->affected_rows > 0;
    }
}