<?php


namespace JSON_Web_Token;


use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Models\User;
use Utils\ResponseCodes;

class JWTController
{
    /**
     * @param User $user
     * Generates a new token and sends to the user
     */
    public static function tokenResponse(User $user)
    {
        $now = time();
        $payload = [
            "iat" => $now,
            "exp" => $now + 24 * 60 * 60,//1 day
            "iss" => JWTConfig::ISSUER,
            "user" => array(
                "name" => $user->getName(),
                "email" => $user->getEmail(),
                "role" => $user->getRole()
            )
        ];
        $jwtToken = JWT::encode($payload, JWTConfig::KEY, JWTConfig::ALG);
        echo json_encode(["message" => "login successful", "access_token" => $jwtToken]);
    }

    /**
     * @param $token string The JWT string received from the user
     * @return false|User Returns False if the token is empty or expired otherwise returns an User which password field has a dummy value. Also sends response to client if token is invalid or expired
     */
    public static function validateToken(string $token)
    {
        $msg = "No Access Token Provided";
        $resp_code = ResponseCodes::HTTP_UNAUTHORIZED;
        if (!empty($token)) {
            try {
                $data = JWT::decode($token, JWTConfig::KEY, [JWTConfig::ALG]);
                return new User($data->user->name, $data->user->email, "Not Necessary", $data->user->role);
            } catch (Exception $ex) {
                $resp_code = $ex instanceof ExpiredException ? ResponseCodes::HTTP_FORBIDDEN : ResponseCodes::HTTP_UNAUTHORIZED;
                $msg = $ex->getMessage();
            }
        }
        http_response_code($resp_code);
        echo json_encode(['message' => $msg]);
        return false;
    }
}