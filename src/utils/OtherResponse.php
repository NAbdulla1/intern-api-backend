<?php


namespace Utils;


class OtherResponse
{
    public static function send(int $responseCode, string $message)
    {
        http_response_code($responseCode);
        echo json_encode(["message" => $message]);
    }
}