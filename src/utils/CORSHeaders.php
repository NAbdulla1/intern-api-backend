<?php


namespace Utils;


class CORSHeaders
{
    static function setProperResponseHeadersForCors(): void
    {
        http_response_code(200);
        header("Access-Control-Allow-Origin: http://localhost:3000");// the origin to allow access from to this api
        header("Access-Control-Expose-Headers: Content-Length");// send content-length as response to the request
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'options') == 0) {
            header("Access-Control-Allow-Headers: Content-Type,access_token");// as response to Access-Control-Request-Headers of preflight request
            header("Access-Control-Allow-Methods: GET,POST,DELETE,PATCH,OPTIONS,PUT,HEAD");// as response to Access-Control-Request-Method of preflight request
            header('Access-Control-Max-Age: 3600');
            exit(0);//early exit if the request is a preflight check ie. OPTIONS request before original request, otherwise a 400 bad request will be thrown
        }
        header("Content-Type: application/json");
    }
}