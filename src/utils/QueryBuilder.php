<?php


namespace Utils;


class QueryBuilder
{

    private int $page = 1;
    private int $pageSize = 10;
    private string $conjunction = " WHERE ";
    private string $query;
    private string $types = "";
    private array $bindParams = [];
    private string $tableName;
    private bool $addPagination;
    private array $parameters;//associative array

    /**
     * QueryBuilder constructor.
     * @param string $tableName
     * @param bool $addPagination
     * @param array $parameters
     */
    public function __construct(string $tableName, bool $addPagination, array $parameters)
    {
        $this->tableName = $tableName;
        $this->addPagination = $addPagination;
        $this->parameters = $parameters;
        $this->query = "SELECT * FROM $tableName";
    }


    public function buildQuery()
    {
        if (!empty($this->parameters['category'])) $this->addCategory();
        if (!empty($this->parameters['price_low'])) $this->addPriceLow();
        if (!empty($this->parameters['price_high'])) $this->addPriceHigh();
        if ($this->addPagination) {
            if (!empty($this->parameters['page'])) $this->page = $this->parameters['page'];
            if (!empty($this->parameters['page_size'])) $this->pageSize = $this->parameters['page_size'];
            $this->addPage();
        }
        return [$this->query, $this->types, $this->bindParams];
    }

    private function addCategory()
    {
        $this->query .= $this->conjunction . "category=?";
        $this->types .= "s";
        array_push($this->bindParams, $this->parameters['category']);
        $this->conjunction = " AND ";
    }

    private function addPriceLow()
    {
        $this->query .= $this->conjunction . "price>=?";
        $this->types .= "d";
        array_push($this->bindParams, $this->parameters['price_low']);
        $this->conjunction = " AND ";
    }

    private function addPriceHigh()
    {
        $this->query .= $this->conjunction . "price<=?";
        $this->types .= "d";
        array_push($this->bindParams, $this->parameters['price_high']);
        $this->conjunction = " AND ";
    }

    private function addPage()
    {
        $offset = max(0, ($this->page - 1) * $this->pageSize);
        $limit = $this->pageSize;
        $this->query .= " LIMIT ? OFFSET ?";
        $this->types .= "ii";
        array_push($this->bindParams, $limit);
        array_push($this->bindParams, $offset);
    }
}