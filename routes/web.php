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

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('/version', function () use ($router) {
        return $router->app->version();
    });

    $router->group(['prefix' => 'v1'], function () use ($router) {
        $router->post('/login', 'UserController@login');
        $router->post('/register', 'UserController@register');
        $router->get('/refreshToken', 'UserController@refreshToken');

        $router->group(['prefix' => 'user', 'middleware' => 'auth.jwt'], function () use ($router) {
            $router->get('/get-user', 'UserController@getUser');
            $router->get('/loginUser', 'UserController@loginUser');
            $router->get('/logoutUser', 'UserController@logoutUser');
        });
    });
});
