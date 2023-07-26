<?php

$router = app()->router;
// frontend to API routes
$router->group(['prefix' => '', 'middleware' => 'auth'], function ($router) {
    $router->post('/transaction-sample', 'FrontApiController@getTransaction');
    $router->post('/analyse-sample', 'FrontApiController@sendAnalyseSample');
    $router->post('/store-fraud', 'FrontApiController@sendStoreFraud');
});
