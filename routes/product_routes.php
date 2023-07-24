<?php

$router = app()->router;

$router->group(['prefix' => '', 'middleware' => 'auth'], function ($router) {
    $router->get('/products/tac_search', 'ProductController@searchDevices');
    $router->get('/products/full_search', 'ProductController@searchDeviceComplete');
});
