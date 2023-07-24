<?php

namespace App\Bussiness\Helpers;

use App\Bussiness\Helpers\ClientHttpInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class GuzzleHelper implements ClientHttpInterface
{

    public static function create(): PendingRequest
    {
        $clientServiceConfig = config('apiServices.pitzi');
        $cliente = $clientServiceConfig['client'];

        $url = $cliente['base_uri'];
        $usuario = $cliente['usuario'];
        $password = $cliente['password'];

        return Http::withHeaders(self::headers())
            ->baseUrl($url)
            ->withBasicAuth($usuario, $password);
    }

    public static function get(string $uri, array $parameters = [])
    {
        return self::create()->get($uri, $parameters);
    }

    public static function post(string $uri, array $data)
    {
        return self::create()->post($uri, $data);
    }

    public static function put(string $uri, array $data)
    {
        return self::create()->put($uri, $data);
    }

    public static function delete(string $uri, array $data = [])
    {
        return self::create()->delete($uri, $data);
    }

    public static function headers(array $headers = [])
    {
        return empty($headers) ? [
            "Content-Type" => "application/json; charset=utf-8",
            "Accept" => "application/json"] : $headers;
    }
}
