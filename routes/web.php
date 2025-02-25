<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/edit_account', [App\Http\Controllers\UserController::class, 'show'])->name('edit_account');