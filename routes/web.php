<?php

// use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('app', function () {
    return view('layouts/app');
});


Route::get('/admin/mindmap', function () {
    return view('admin/mindmap');
});

Route::get('/api/menu_type', [App\Http\Controllers\AlfredController::class, 'type']);
Route::post('/api/create', [App\Http\Controllers\AlfredController::class, 'create']);
Route::get('/api/goto', [App\Http\Controllers\AlfredController::class, 'goto']);
Route::get('/api/todo/list', [App\Http\Controllers\AlfredController::class, 'todo_list']);
Route::get('/api/todo/add', [App\Http\Controllers\AlfredController::class, 'todo_add']);
Route::get('/api/todo/status', [App\Http\Controllers\AlfredController::class, 'todo_status']);
Route::get('/api/todo/detail', [App\Http\Controllers\AlfredController::class, 'todo_detail']);
Route::get('/api/notify_word', [App\Http\Controllers\AlfredController::class, 'notify_word']);
Route::get('/api/notify_joke', [App\Http\Controllers\AlfredController::class, 'notify_joke']);
Route::get('/api/words/update', [App\Http\Controllers\ApiWordController::class, 'update']);
Route::get('/api/words/google', [App\Http\Controllers\ApiWordController::class, 'google']);
Route::get('/api/words/sound', [App\Http\Controllers\ApiWordController::class, 'sound']);
Route::get('/api/sentences/update', [App\Http\Controllers\ApiSentenceController::class, 'update']);
Route::get('/admin/auto-login', [App\Http\Controllers\AutoLoginController::class, 'autoLogin']);
Route::get('/api/mindmap/query', [App\Http\Controllers\ApiMindMapController::class, 'query']);
Route::post('/api/mindmap/create', [App\Http\Controllers\ApiMindMapController::class, 'create']);
Route::post('/api/mindmap/update', [App\Http\Controllers\ApiMindMapController::class, 'update']);
Route::get('/api/article', [App\Http\Controllers\ApiArticleController::class, 'index']);
