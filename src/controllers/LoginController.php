<?php


namespace Controller;


use Models\User;
use Repository\UserRepository;
use Utils\ResponseCodes;
use JSON_Web_Token\JWTController;

class LoginController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function login($credentialsArray)
    {
        if ($this->isEmptyCredentials($credentialsArray)) return;
        $user = $this->userRepository->getOne($credentialsArray['email']);
        if (empty($user) || empty($user['email']) || empty($user['password']) || !$this->isUserVerified($user, $credentialsArray)) {
            http_response_code(ResponseCodes::HTTP_UNAUTHORIZED);
            echo json_encode(["message" => "Incorrect email or password"]);
        }
    }

    private function isEmptyCredentials($credentialsArray): bool
    {
        if (empty($credentialsArray) || empty($credentialsArray['email']) || empty($credentialsArray['password'])) {
            http_response_code(ResponseCodes::HTTP_BAD_REQUEST);
            return true;
        }
        return false;
    }

    private function isUserVerified($user, $credentialsArray): bool
    {
        if (password_verify($credentialsArray['password'], $user['password'])) {
            JWTController::tokenResponse(User::fromAssocArray($user));
            return true;
        }
        return false;
    }
}