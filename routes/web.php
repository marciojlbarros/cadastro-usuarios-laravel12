<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImportCsvUserController;

Route::get('/', function () {
    return view('welcome');
})->name('dashboard');

Route::get('/index-user', [UserController::class, 'index'])->name('user.index');
Route::get('/show-user/{user}', [UserController::class, 'show'])->name('user.show');

Route::get('/create-user', [UserController::class, 'create'])->name('user.create');
Route::post('/store-user', [UserController::class, 'store'])->name('user.store');

Route::get('/edit-user/{user}', [UserController::class, 'edit'])->name('user.edit');
Route::put('/update-user/{user}', [UserController::class, 'update'])->name('user.update');

Route::get('/edit-user-password/{user}', [UserController::class, 'editPassword'])->name('user.edit-password');
Route::put('/update-user-password/{user}', [UserController::class, 'updatePassword'])->name('user.update-password'); 

Route::delete('/delete-user/{user}', [UserController::class, 'destroy'])->name('user.destroy');

Route::get('/generate-pdf-user/{user}', [UserController::class, 'generatePdf'])->name('user.generate-pdf');
Route::get('/generate-pdf-user', [UserController::class, 'generatePdfUsers'])->name('user.generate-pdf-users');
Route::get('/generate-csv-user', [UserController::class, 'generateCsvUsers'])->name('user.generate-csv-users');

Route::post('/import-csv-user', [ImportCsvUserController::class, 'importCsvUsers'])->name('user.import-csv-users');