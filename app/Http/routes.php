<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
//return "Hello world";
    return redirect('task');
});

Route::get('task', 'TaskController@listTasks');
Route::post('task', 'TaskController@addTask');
Route::put('task', 'TaskController@updateTask');
Route::put('task/update', 'TaskController@updateTaskStatus');
