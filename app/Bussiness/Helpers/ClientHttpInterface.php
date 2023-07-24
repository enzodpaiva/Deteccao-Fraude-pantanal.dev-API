<?php

namespace App\Bussiness\Helpers;

interface ClientHttpInterface
{
    public static function get(string $uri, array $parameters = []);

    public static function post(string $uri, array $data);

    public static function put(string $uri, array $data);

    public static function delete(string $uri, array $data = []);
}
