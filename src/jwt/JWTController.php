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
     * Sends the token to the user
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
     * @return false|user Returns False if the token is empty or expired otherwise returns an object having three field 'name', 'email', 'role'. Also sends response to client if token is invalid or expired
     */
    public static function validateToken(string $token)
    {
        header("Content-Type: application/json");
        $msg = "No Access Token Provided";
        $resp_code = ResponseCodes::HTTP_UNAUTHORIZED;
        if (!empty($token)) {
            try {
                $data = JWT::decode($token, JWTConfig::KEY, [JWTConfig::ALG]);
                return $data->user;
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