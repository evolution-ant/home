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
        'joke' => JokeController::class,
        'type' => TypeController::class,
        'tag' => TagController::class,
        'test' => TestController::class,
    ]);
    $router->get('/', 'HomeController@index')->name('home');
    $router->get('/chartjs', 'ChartjsController@index')->name('chartjs');
});
