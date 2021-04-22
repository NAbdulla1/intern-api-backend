<?php


namespace Database;
use mysqli;

require "../../vendor/autoload.php";

class DB
{
    private static $connection = null;

    private function __construct()
    {
    }

    public static function getConnection(): mysqli
    {
        if (self::$connection == null) {
            self::$connection = new mysqli(
                config::HOST,
                config::USER_NAME,
                config::USER_PASSWORD,
                config::DB_NAME,
                config::PORT);
        }
        return self::$connection;
    }
}