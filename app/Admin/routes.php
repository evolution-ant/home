<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {
    $router->resources([
        'todo' => TodoController::class,
        'joke' => JokeController::class,
        'code' => CodeController::class,
        'type' => TypeController::class,
        'tag' => TagController::class,
        'test' => TestController::class,
    ]);
    // 加入自己的管理路由
    $router->namespace('Auth')->group(function ($router) {
        $router->resource('auth/users', 'UserController');
        $router->resource('auth/roles', 'RoleController');
        $router->resource('auth/permissions', 'PermissionController');
        $router->resource('auth/menu', 'MenuController', ['except' => ['create']]);
        $router->resource('auth/logs', 'LogController', ['only' => ['index', 'destroy']]);
    });

    $router->get('/', 'HomeController@index')->name('home');
    $router->get('/chartjs', 'ChartjsController@index')->name('chartjs');
});
