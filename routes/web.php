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

        $router->group(['middleware' => 'auth.jwt'], function () use ($router) {
            $router->group(['prefix' => 'user'], function () use ($router) {
                $router->get('/get-user', 'UserController@getUser');
                $router->get('/', 'UserController@index');
                $router->get('/logout', 'UserController@logout');
            });

            $router->group(['prefix' => 'mahasiswa'], function () use ($router) {
                $router->get('/', 'MahasiswaController@index');
                $router->get('/{id}', 'MahasiswaController@show');
                $router->post('/', 'MahasiswaController@store');
                $router->put('/{id}', 'MahasiswaController@update');
                $router->delete('/{id}', 'MahasiswaController@destroy');
            });
        });

    });
});
