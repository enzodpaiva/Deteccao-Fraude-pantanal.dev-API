<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */

$router->get('/', function () use ($router) {
    return response()->json(['sucess' => '200'], 200);
});

$router->get('/favicon.ico', function () use ($router) {
    return;
});

require __DIR__ . '/fraud_Front_API_routes.php';
