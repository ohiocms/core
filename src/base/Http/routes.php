<?php

/**
 * Front
 */
Route::group(['middleware' => ['web']], function () {
    Route::get('/', function () {
        return view('ohio-core::base.front.home');
    });
});

/**
 * Admin
 */
Route::group([
    'prefix' => 'admin',
    'middleware' => ['web', 'auth.admin']
],
    function () {
        Route::get('/', \Ohio\Core\Base\Http\Controllers\AdminController::class . '@getIndex');
        Route::get('/ohio/core/{any?}/{something?}/{c?}', function () {
            return view('ohio-core::layouts.admin.dashboard');
        });
    }
);

/**
 * Admin-User
 */
Route::group([
    'prefix' => 'admin-user',
    'middleware' => ['web']
],
    function () {
        Route::get('/', \Ohio\Core\Base\Http\Controllers\AdminUserController::class . '@getIndex');
    }
);