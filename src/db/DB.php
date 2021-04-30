<?php


namespace Database;

use MyLogger\Log;
use mysqli;
use mysqli_stmt;

require "../../vendor/autoload.php";

class DB
{
    private static mysqli $connection;
    private static DB $database;

    private function __construct()
    {
    }

    public static function instance(): DB
    {
        if (!isset(self::$database))
            self::$database = new DB();
        return self::$database;
    }

    public function getConnection(): mysqli
    {
        if (!isset(self::$connection)) {
            self::$connection = new mysqli(
                config::HOST,
                config::USER_NAME,
                config::USER_PASSWORD,
                config::DB_NAME,
                config::PORT);
        }
        return self::$connection;
    }

    /**
     * @param string $paramQuery The query, as a string. You should not add a terminating semicolon or \g to the statement. This parameter can include one or more parameter markers in the SQL statement by embedding question mark (?) characters at the appropriate positions.
     * @param string $types A string that contains one or more characters which specify the types for the corresponding bind variables
     * @param array $values The variables to bind the given parameters
     * @return false|mysqli_stmt The statement is executed
     */
    public function executePreparedStatement(string $paramQuery, string $types, array $values)
    {
        assert(strlen($types) > 0 && strlen($types) == count($values) && strlen($types) == substr_count($paramQuery, "?"));
        $stmt = DB::getConnection()->prepare($paramQuery);
        if (!$stmt || !$stmt->bind_param($types, ...$values)) {
            Log::dbg(DB::instance()->getConnection()->error);
            return false;
        }
        if (!$stmt->execute()) {
            Log::dbg(DB::instance()->getConnection()->error);
            return false;
        }
        return $stmt;
    }
}