<?php

$router = app()->router;

$router->group(['prefix' => '', 'middleware' => 'auth'], function ($router) {

});
