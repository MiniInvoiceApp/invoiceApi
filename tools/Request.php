<?php

class Request
{
    /**
     * Get the request method
     *
     * @return mixed
     */
    public static function method()
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    /**
     * Get request url parameters
     *
     * @return array
     */
    public static function urlParams()
    {
        return $_REQUEST;
    }

    /**
     * Get request body
     *
     * @return object
     */
    public static function jsonBody()
    {
        return json_decode(file_get_contents("php://input"));
    }

    /**
     * Based on method get request parameters
     *
     * @return array|object
     */
    public static function data()
    {
        if (self::method() === "GET") {
            $result = self::urlParams();
        } else {
            $result = self::jsonBody();
        }

        return $result;
    }
}
