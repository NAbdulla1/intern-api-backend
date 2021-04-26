<?php


namespace Utils;


class CORSHeaders
{
    static function setProperResponseHeadersForCors(): void
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Access-Control-Request-Method,Access-Control-Allow-Headers,Origin,Content-Type,access_token,Access-Control-Request-Headers");
        header("Access-Control-Allow-Methods: GET,POST,DELETE,PATCH,OPTIONS,PUT,HEAD");
        header("Access-Control-Expose-Headers: Content-Length");
        header("Content-Type: application/json");
        http_response_code(200);
    }
}