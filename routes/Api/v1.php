<?php

use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VegetableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(UserController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::get('avatars', 'getAvatars');
});

Route::controller(VegetableController::class)->group(function () {
    Route::get('save-history/{vegetable_id}/{user_id}', 'saveHistory');
});

Route::middleware(['auth:api'])->group(function () {
    // User Controller
    Route::controller(UserController::class)->group(function () {
        Route::get('user', 'index');
        Route::post('logout', 'logout');
    });

    // Vegetable Controller
    Route::controller(VegetableController::class)->group(function () {
        Route::get('vegetables', 'getAllVegetable');
        Route::get('vegetables/{id}', 'getDetailVegetable');
        Route::post('vegetables/{id}/save', 'saveVegetable');
    });

    // Home Controller
    Route::controller(HomeController::class)->group(function () {
        Route::get('home', 'index');
        Route::get('home/histories', 'homeHistories');
        Route::get('home/types', 'homeTypes');
        Route::get('types', 'types');
        Route::get('histories', 'histories');
        Route::get('saveds', 'saveds');
        Route::post('predict', 'predict');
    });
});
