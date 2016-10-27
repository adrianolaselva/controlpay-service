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
    return "Componente de integração ControlPay (File change) v0.1.0";
});

$app->group(['prefix' => '/v1', 'middleware' => 'auth'], function() use ($app) {

    $app->get('callbacks/{id}', 'App\Http\Controllers\CallBackController@load');
    $app->get('callbacks', 'App\Http\Controllers\CallBackController@listAll');

    $app->get('requests/{id}', 'App\Http\Controllers\RequestController@load');
    $app->get('requests', 'App\Http\Controllers\RequestController@listAll');

    $app->post('file/upload/{path}/{name}', 'App\Http\Controllers\FileController@upload');
    $app->post('file/{path}/{name}', 'App\Http\Controllers\FileController@add');
    $app->get('file/{path}/{name}', 'App\Http\Controllers\FileController@load');
    $app->delete('file/{path}/{name}', 'App\Http\Controllers\FileController@delete');

});

$app->group(['prefix' => '/v1'], function() use ($app) {

    $app->get('callbacks/controlpay/intencaovendacallback', 'App\Http\Controllers\CallBackController@controlPayIntencaoVendaCallBack');

});



