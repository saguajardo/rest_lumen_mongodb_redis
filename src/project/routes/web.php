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

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    // List of all tasks
    $router->get('task', 'TaskController@index');

    // List tasks by filters
	$router->post('list', 'TaskController@list');

    // Create a new task
    $router->post('task', 'TaskController@store');

    // Update a task
    $router->put('task', 'TaskController@edit');

    // Delete a task
    $router->delete('task', 'TaskController@destroy');
});