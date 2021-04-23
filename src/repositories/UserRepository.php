<?php


namespace Repository;


use Database\DB;
use Models\User;

class UserRepository
{
    private DB $database;

    public function __construct()
    {
        $this->database = DB::instance();
    }

    public function getOne($email): ?array
    {
        $stmt = $this->database->executePreparedStatement("SELECT * FROM user WHERE email = ?", "s", [$email]);
        if (!$stmt) return null;
        $result_set = $stmt->get_result();
        if ($user = $result_set->fetch_assoc())
            return $user;
        return null;
    }

    public function create(User $user): bool
    {
        $stmt = $this->database->executePreparedStatement("INSERT INTO user(name, email, password, role) VALUES (?, ?, ?, ?)",
            "ssss", [$user->getName(), $user->getEmail(), $user->getPassword(), $user->getRole()]);
        return $stmt && $stmt->affected_rows > 0;
    }
}