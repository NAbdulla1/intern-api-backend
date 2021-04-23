<?php

namespace Controller;

require "../../vendor/autoload.php";

use Exception;
use Models\User;
use Repository\UserRepository;
use Utils\ResponseCodes;

class UserController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function create($userAssocArray)
    {
        header("Content-type: application/json");
        try {
            $user = User::fromAssocArray($userAssocArray);
            if ($this->isUserExists($user)) return;
            if ($this->registerUser($user)) return;
            else {
                http_response_code(ResponseCodes::HTTP_INTERNAL_SERVER_ERROR);
                echo json_encode(["message" => "Can't Process Request"]);
            }
        } catch (Exception $ex) {
            http_response_code(ResponseCodes::HTTP_BAD_REQUEST);
            echo json_encode(["message" => $userAssocArray == null ? "No Data" : $ex->getMessage()]);
        }
    }

    private function isUserExists($user): bool
    {
        if ($this->userRepository->getOne($user->getEmail())) {
            http_response_code(ResponseCodes::HTTP_BAD_REQUEST);
            echo json_encode(['message' => 'User already exists']);
            return true;
        }
        return false;
    }

    private function registerUser(User $user): bool
    {
        $user->setPassword(self::hashPassword($user->getPassword()));
        if ($this->userRepository->create($user)) {
            http_response_code(ResponseCodes::HTTP_CREATED);
            echo json_encode($user->toAssocArray());
            return true;
        }
        return false;
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}