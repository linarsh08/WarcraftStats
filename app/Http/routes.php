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

$app->group(['prefix' => 'users'], function () use ($app) {
    // Register new user
    $app->post('/', ['uses' => 'UsersController@create']);
    // Get token via credentials (username, password)
    $app->post('/login', ['uses' => 'UsersController@login']);

    $app->group(['middleware' => 'auth'], function () use ($app) {
        // Users index with pagination
        $app->get('/', ['uses' => 'UsersController@index']);
        // Show user information
        $app->get('/{id}', ['uses' => 'UsersController@show']);
        // Update user's information (password)
        $app->put('/{id}', ['uses' => 'UsersController@update']);
        // Delete user (delete)
        $app->delete('/{id}', ['uses' => 'UsersController@delete']);
    });
});

$app->group(['prefix' => 'users/{user_id}/build-orders'], function () use ($app) {
    $app->group(['middleware' => 'auth'], function () use ($app) {
        // Get user's build orders
        $app->get('/', ['uses' => 'BuildOrdersController@indexByUser']);
    });
});

$app->group(['prefix' => 'build-orders'], function () use ($app) {
    // Build orders index with pagination
    $app->get('/', ['uses' => 'BuildOrdersController@index']);
    // Show build order information
    $app->get('/{id}', ['uses' => 'BuildOrdersController@show']);

    $app->group(['middleware' => 'auth'], function () use ($app) {
        // Create build order, attached to current user
        $app->post('/', ['uses' => 'BuildOrdersController@create']);
        // Update build order, attached to current user
        $app->put('/{id}', ['uses' => 'BuildOrdersController@update']);
        // Delete current user's build orders
        $app->delete('/{id}', ['uses' => 'BuildOrdersController@delete']);
    });
});
