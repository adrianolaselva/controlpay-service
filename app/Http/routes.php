<?php

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

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->group(['prefix' => '/v1', 'middleware' => 'auth'], function() use ($app) {

    $app->get('callbacks/{id}', 'CallBackController@load');
    $app->get('callbacks', 'CallBackController@listAll');

    $app->get('requests/{id}', 'RequestController@load');
    $app->get('requests', 'RequestController@listAll');

    $app->get('files/{id}', 'FileController@load');
    $app->get('files', 'FileController@listAll');
    $app->get('files/download/{path}/{name}', 'FileController@download');
    $app->post('files/add/{path}/{name}', 'FileController@add');
    $app->post('files/upload/{path}', 'FileController@upload');
    $app->delete('files/{path}/{name}', 'FileController@delete');

});

$app->group(['prefix' => '/v1'], function() use ($app) {
    $app->post('callbacks/controlpay/intencaovendacallback', 'CallBackController@intencaoVendaCallBack');
    $app->get('callbacks/controlpay/intencaovendacallback', 'CallBackController@intencaoVendaCallBack');
});



