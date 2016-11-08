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

    $app->get('callbacks/{id}', 'App\Http\Controllers\CallBackController@load');
    $app->get('callbacks', 'App\Http\Controllers\CallBackController@listAll');

    $app->get('requests/{id}', 'App\Http\Controllers\RequestController@load');
    $app->get('requests', 'App\Http\Controllers\RequestController@listAll');

    $app->get('files/{id}', 'App\Http\Controllers\FileController@load');
    $app->get('files', 'App\Http\Controllers\FileController@listAll');
    $app->get('files/download/{path}/{name}', 'App\Http\Controllers\FileController@download');
    $app->post('files/add/{path}/{name}', 'App\Http\Controllers\FileController@add');
    $app->post('files/upload/{path}', 'App\Http\Controllers\FileController@upload');
    $app->delete('files/{path}/{name}', 'App\Http\Controllers\FileController@delete');

});

$app->group(['prefix' => '/v1'], function() use ($app) {
    $app->post('callbacks/controlpay/intencaovendacallback', 'App\Http\Controllers\CallBackController@intencaoVendaCallBack');
    $app->get('callbacks/controlpay/intencaovendacallback', 'App\Http\Controllers\CallBackController@intencaoVendaCallBack');
});



