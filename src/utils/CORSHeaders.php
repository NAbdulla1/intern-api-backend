<?php


namespace Utils;


class CORSHeaders
{
    static function setProperResponseHeadersForCors(): void
    {
        http_response_code(200);
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'options') == 0) {
            header("Access-Control-Allow-Origin: http://localhost:3000");
            header("Access-Control-Allow-Headers: Origin,Content-Type,access_token");
            header("Access-Control-Allow-Methods: GET,POST,DELETE,PATCH,OPTIONS,PUT,HEAD");
            header("Access-Control-Expose-Headers: Content-Length");
            exit(0);//early exit if the request is a preflight check, otherwise a 400 bad request will be thrown
        }
        header("Content-Type: application/json");
    }
}