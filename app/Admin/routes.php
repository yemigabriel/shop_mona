<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    
    $router->resource('users', UserController::class);
    $router->resource('categories', CategoryController::class);
    $router->resource('products', ProductController::class);
    $router->resource('orders', OrderController::class);
    $router->resource('order_items', OrderItemController::class);
    $router->get('order/{id}', 'OrderDetailController@index');
    $router->resource('blog', BlogController::class);

});
