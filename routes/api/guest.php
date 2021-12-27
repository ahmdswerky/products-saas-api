<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::post('signin', [AuthController::class, 'signin'])->name('signin');
Route::post('signup', [AuthController::class, 'signup'])->name('signup');
Route::post('signout', [AuthController::class, 'signout'])->name('signout');
Route::get('user', [AuthController::class, 'user'])->middleware('auth:api')->name('user');
Route::put('user', [AuthController::class, 'update'])->middleware('auth:api')->name('user.update');
