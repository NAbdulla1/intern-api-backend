<?php

namespace Controller;

require "../../vendor/autoload.php";

use Exception;
use Models\User;
use Repository\UserRepository;
use Utils\OtherResponse;
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
        try {
            $user = User::fromAssocArray($userAssocArray);
            if ($this->isUserExists($user)) return;
            if ($this->registerUser($user)) return;
            else OtherResponse::send(ResponseCodes::HTTP_INTERNAL_SERVER_ERROR, "Can't Process Request");
        } catch (Exception $ex) {
            OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, $userAssocArray == null ? "No Data" : $ex->getMessage());
        }
    }

    private function isUserExists($user): bool
    {
        if ($this->userRepository->getOne($user->getEmail())) {
            OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, 'User already exists');
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

    public function getAll(string $page, string $pageSize)
    {
        if (empty($page) || empty($pageSize) || ((int)$page) <= 0 || ((int)$pageSize) <= 0) {
            OtherResponse::send(ResponseCodes::HTTP_BAD_REQUEST, "page and page_size parameters has invalid parameters");
            return;
        }
        list($usersAsAssocArray, $usersCount) = $this->userRepository->get((int)$page, (int)$pageSize);
        echo json_encode(["users" => $usersAsAssocArray, 'count' => $usersCount]);
    }
}