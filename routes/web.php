<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/edit_account', [App\Http\Controllers\UserController::class, 'show'])->name('edit_account');
Route::get('/admin/areas_of_knolwedge/show', [App\Http\Controllers\AdminController::class, 'showAreaOfKnowledgeForm'])->name('admin.areas_of_knowledge.show');
Route::post('/admin/areas_of_knolwedge/create', [App\Http\Controllers\AdminController::class, 'createAreaOfKnowledge'])->name('admin.areas_of_knowledge.create');
Route::get('/admin/modules/show', [App\Http\Controllers\AdminController::class, 'showModuleForm'])->name('admin.create_new_module.show');
Route::post('/admin/modules/create', [App\Http\Controllers\AdminController::class, 'createModule'])->name('admin.create_new_module.create');