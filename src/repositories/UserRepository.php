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

    public function get(int $page, int $pageSize): array
    {
        $result_set = $this->database->getConnection()->query("SELECT name, email, role FROM user");
        $rows = $result_set ? $result_set->num_rows : 0;
        $users = [];
        $stmt = $this->database->executePreparedStatement("SELECT * FROM user LIMIT ? OFFSET ?", "ii", [$pageSize, max(0, $pageSize * ($page - 1))]);
        if ($stmt) $result_set = $stmt->get_result();
        else return [$users, $rows];
        while ($result_set && $order = $result_set->fetch_assoc())
            array_push($users, User::fromAssocArray($order)->toAssocArray());
        return [$users, $rows === null ? count($users) : $rows];
    }
}