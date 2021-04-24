<?php


namespace Models;


use InvalidArgumentException;

class User
{
    private string $name;
    private string $email;
    private string $password;
    private string $role;

    public function __construct(string $name,
                                string $email,
                                string $password,
                                string $role)
    {
        $this->setName($name);
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setRole($role);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->checkStringConstraint("name", $name, 1, 100);
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->checkStringConstraint("email", $email, 1, 100);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new InvalidArgumentException("Invalid Email Address");
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->checkStringConstraint("password", $password, 1, 100);
        $this->password = $password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->checkStringConstraint("role", $role, 1, 10);
        $this->role = $role;
    }

    private function checkStringConstraint($fieldName, $field, $min, $max)
    {
        if ($min <= strlen($field) && strlen($field) <= $max) return;
        throw new InvalidArgumentException("User $fieldName length must be in between $min and $max");
    }

    public static function fromAssocArray($userAssocArray): User
    {
        $userAssocArray = self::fillMissingValue($userAssocArray);
        return new User(
            $userAssocArray['name'],
            $userAssocArray['email'],
            $userAssocArray['password'],
            $userAssocArray['role']
        );
    }

    private static function fillMissingValue($assocArray)
    {
        $keys = ["name", "email", "password", "role"];
        foreach ($keys as $key)
            if (!isset($assocArray[$key])) $assocArray[$key] = "";//they will fail in validation check
        return $assocArray;
    }

    public function toAssocArray(): array
    {
        return ['name' => $this->name, 'email' => $this->email, 'role' => $this->role];
    }

    public function isAdmin(): bool
    {
        return strcasecmp($this->role, "admin") == 0;
    }
}