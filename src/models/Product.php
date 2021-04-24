<?php


namespace Models;


use InvalidArgumentException;

class Product
{
    private string $name;
    private string $sku;
    private string $description;
    private string $category;
    private float $price;
    private string $imageUrl;

    public function __construct(string $name,
                                string $sku,
                                string $description,
                                string $category,
                                float $price,
                                string $imageUrl)
    {
        $this->setName($name);
        $this->setSku($sku);
        $this->setDescription($description);
        $this->setCategory($category);
        $this->setPrice($price);
        $this->setImageUrl($imageUrl);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->checkStringConstraint("name", $name, 1, 50);
        $this->name = $name;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): void
    {
        $this->checkStringConstraint("SKU", $sku, 1, 20);
        $this->sku = $sku;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->checkStringConstraint("description", $description, 1, 500);
        $this->description = $description;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->checkStringConstraint("category", $category, 1, 20);
        $this->category = $category;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        if ($price <= 0)
            throw new InvalidArgumentException("Price must be greater than 0");
        $this->price = $price;
    }

    public function getImageUrl(): string
    {
        $this->checkStringConstraint("imageUrl", $this->imageUrl, 1, 100);
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    private function checkStringConstraint($fieldName, $field, $min, $max)
    {
        if ($min <= strlen($field) && strlen($field) <= $max) return;
        throw new InvalidArgumentException("Product $fieldName length must be in between $min and $max");
    }

    public static function fromAssocArray($prod): Product
    {
        $prod = self::fillMissingValue($prod);
        return new Product(
            $prod['name'],
            $prod['sku'],
            $prod['description'],
            $prod['category'],
            (float)$prod['price'],
            $prod['imageUrl'],
        );
    }

    private static function fillMissingValue($assocArray)
    {
        $keys = ["name", "sku", "description", "category", "price", "imageUrl"];
        foreach ($keys as $key)
            if (!isset($assocArray[$key])) $assocArray[$key] = $key != "price" ? "" : "0";//they will fail in validation check
        return $assocArray;
    }

    public function toAssocArray(): array
    {
        return ["name" => $this->getName(), "sku" => $this->getSku(),
            "description" => $this->getDescription(), "category" => $this->getCategory(),
            "price" => $this->getPrice(), "imageUrl" => $this->getImageUrl()];
    }
}