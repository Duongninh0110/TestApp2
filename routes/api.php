<?php

use Illuminate\Http\Request;

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

//Member Routes
Route::resource('members','MemberController');

//Project Routes
Route::resource('projects','ProjectController');

//Assignment Routes
Route::get('assignments', 'AssignmentController@index');
Route::post('assignments', 'AssignmentController@store');
Route::get('assignments/project/{id}', 'AssignmentController@listMembers');
Route::get('assignments/member/{id}', 'AssignmentController@listProjects');
Route::delete('assignments/project/{project_id}/member/{member_id}', 'AssignmentController@destroy');
Route::put('assignments/project/{project_id}/member/{member_id}', 'AssignmentController@update');