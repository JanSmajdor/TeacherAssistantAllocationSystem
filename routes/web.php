<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/edit_account', [App\Http\Controllers\UserController::class, 'show'])->name('edit_account');
Route::post('/request_edit_account', [App\Http\Controllers\UserController::class, 'edit'])->name('request_edit_account');
Route::get('/admin/areas_of_knolwedge/show', [App\Http\Controllers\AdminController::class, 'showAreaOfKnowledgeForm'])->name('admin.areas_of_knowledge.show');
Route::post('/admin/areas_of_knolwedge/create', [App\Http\Controllers\AdminController::class, 'createAreaOfKnowledge'])->name('admin.areas_of_knowledge.create');
Route::get('/admin/modules/show', [App\Http\Controllers\AdminController::class, 'showModuleForm'])->name('admin.create_new_module.show');
Route::post('/admin/modules/create', [App\Http\Controllers\AdminController::class, 'createModule'])->name('admin.create_new_module.create');
Route::post('/edit_account/approve', [App\Http\Controllers\HomeController::class, 'approve'])->name('approve_edit_account');
Route::post('/edit_account/deny', [App\Http\Controllers\HomeController::class, 'deny'])->name('deny_edit_account');