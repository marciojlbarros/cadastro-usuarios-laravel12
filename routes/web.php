<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
})->name('dashboard');

Route::get('/index-user', [UserController::class, 'index'])->name('user.index');
Route::get('/create-user', [UserController::class, 'create'])->name('user.create');
Route::post('/store-user', [UserController::class, 'store'])->name('user.store');