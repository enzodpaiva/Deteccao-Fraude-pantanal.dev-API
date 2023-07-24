<?php

$router = app()->router;

$router->post('/bitrix/dealdelete', 'BitrixController@bitrixDealDelete');
$router->post('/bitrix/leaddelete', 'BitrixController@bitrixLeadDelete');

$router->post('/bitrix/leadcreate/{leadId}/{status}/{subStatus}/{createdId}[/{lostSubStatus}]', 'BitrixController@bitrixLeadCreate');
$router->post('/bitrix/dealcreate/{opportunityId}/{status}/{subStatus}/{createdId}[/{lostSubStatus}]', 'BitrixController@bitrixDealCreate');

$router->post('/bitrix/dealupdate/{opportunityId}/{status}/{subStatus}/{createdId}[/{lostSubStatus}]', 'BitrixController@bitrixDealUpdate');
$router->post('/bitrix/leadupdate/{leadId}/{status}/{subStatus}/{createdId}[/{lostSubStatus}]', 'BitrixController@bitrixLeadUpdate');
