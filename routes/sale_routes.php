<?php

$router = app()->router;

$router->group(['prefix' => '', 'middleware' => 'auth'], function ($router) {
    $router->post('/save', 'LeadController@save');
    $router->post('/save/finish', 'LeadController@saveFinish');
    // $router->post('/orders/device-photo-verification/{orderId}', 'LeadController@devicePhotoVerification'); desativado por enquanto
    $router->get('/service-terms/{orderId}', 'LeadController@termsUseSignature');
    $router->delete('/service-terms-cancel/{orderId}', 'LeadController@termsUseSignatureCancel');
    $router->delete('/signature-cancel/{orderId}', 'LeadController@signatureCancel');
    $router->get('/list/opportunity/{cpf}/{email}', 'LeadController@listOpportunitysForClient');
});
