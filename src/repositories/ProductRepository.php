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

    public function get(): array //todo add filter for products, example: price range, category, pagination
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

    public function update($prodAssocArray): bool
    {
        $keys = ["name", "description", "category", "price", "imageUrl"];
        if (!$this->hasUpdate($prodAssocArray, $keys)) return false;
        $stmt = $this->database->executePreparedStatement(
            $this->buildQuery($prodAssocArray, $keys),
            $this->buildTypes($prodAssocArray, $keys),
            $this->buildParameters($prodAssocArray, $keys));
        return $stmt && $stmt->affected_rows == 1;
    }

    private function hasUpdate($prodAssocArray, $keys): bool
    {
        $count = 0;
        foreach ($keys as $key)
            if (isset($prodAssocArray[$key])) $count++;
        return $count > 0;
    }

    private function buildQuery($prodAssocArray, $keys): string
    {
        $updates = [];
        foreach ($keys as $key)
            if (isset($prodAssocArray[$key]))
                array_push($updates, "$key = ?");
        $query = "UPDATE products SET " . join(", ", $updates) . " WHERE sku = ?";
        return $query;
    }

    private function buildParameters($prodAssocArray, $keys): array
    {
        $parameterValues = [];
        foreach ($keys as $key)
            if (isset($prodAssocArray[$key]))
                array_push($parameterValues, $prodAssocArray[$key]);
        array_push($parameterValues, $prodAssocArray['sku']);
        return $parameterValues;
    }

    private function buildTypes($prodAssocArray, $keys): string
    {
        $types = "";
        foreach ($keys as $key)
            if (isset($prodAssocArray[$key]))
                $types .= ($key == "price") ? "d" : "s";
        $types .= "s";
        return $types;
    }
}