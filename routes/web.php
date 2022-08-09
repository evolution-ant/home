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

Route::get('/api/menu_type', [App\Http\Controllers\AlfredController::class, 'type']);
Route::post('/api/create', [App\Http\Controllers\AlfredController::class, 'create']);
Route::get('/api/goto', [App\Http\Controllers\AlfredController::class, 'goto']);
Route::get('/api/words/update', [App\Http\Controllers\ApiWordController::class, 'update']);
Route::get('/api/words/google', [App\Http\Controllers\ApiWordController::class, 'google']);
Route::get('/api/sentences/update', [App\Http\Controllers\ApiSentenceController::class, 'update']);
Route::get('/admin/auto-login', [App\Http\Controllers\AutoLoginController::class, 'autoLogin']);
