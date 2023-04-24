<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Website\ContactController;
use Illuminate\Http\Request;

Route::post('signin', [AuthController::class, 'signin'])->name('signin');
Route::post('signup', [AuthController::class, 'signup'])->name('signup');
Route::post('signout', [AuthController::class, 'signout'])->name('signout');
Route::get('user', [AuthController::class, 'user'])->middleware('auth:api')->name('user');
Route::post('user', [AuthController::class, 'update'])->middleware('auth:api')->name('user.update');
Route::post('contact', [ContactController::class, 'store']);
Route::any('status', function (Request $request) {
    return response([
        'status' => true,
    ]);
});
