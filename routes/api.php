<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('user', function (Request $request) {
    return $request->user();
});

/** Realizar login */
Route::group(['prefix' => 'login', 'as' => 'login'], function () use ($router) {
    $router->post('/', 'UserController@login');
});

/** Registrar usuário */
Route::group(['prefix' => 'register', 'as' => 'register'], function () use ($router) {
    $router->get('/', 'UserController@register');
    $router->post('/', 'UserController@register');
});

/** Registrando url para requisições de recursos */
Route::group(['prefix' => 'authorized', 'middleware' => 'auth:sanctum'], function () use ($router) {
    $router->get('/', 'UserController@isAuth');
});

/** Registrando url para requisições de solictações */
Route::group(['prefix' => 'request', 'middleware' => 'auth:sanctum'], function () use ($router) {
    $router->get('/user/{paged?}', 'RequestController@getOwn'); //Deve estar antes, para priorizar "user" e não confundir com endpoint abaixo "estado"
    $router->get('/{estado?}/{paged?}', 'RequestController@get');
    $router->post('/', 'RequestController@add');
    $router->delete('/{requestID}', 'RequestController@delete');
    $router->post('help', 'RequestController@registerHelp');
});

/** Registrando url para requisições de recursos */
Route::group(['prefix' => 'resource', 'middleware' => 'auth:sanctum'], function () use ($router) {
    $router->post('/', 'ResourcesController@add');
});

/** Registrando url para requisições de unidades */
Route::group(['prefix' => 'unity', 'middleware' => 'auth:sanctum'], function () use ($router) {
    $router->get('/', 'UnityController@get');
    $router->post('/', 'UnityController@add'); 
    $router->delete('/{unityID}', 'UnityController@delete');  
});

/** Carregar Empresas */
Route::group(['prefix' => 'business', 'middleware' => 'auth:sanctum'], function () use ($router) {
    $router->get('/', 'BusinessController@get');
});

/** Carregar Localidades */
Route::group(['prefix' => 'location', 'middleware' => 'auth:sanctum'], function () use ($router) {
    $router->get('/zipcode/{zipcode}', 'LocationController@zipcode');
});
