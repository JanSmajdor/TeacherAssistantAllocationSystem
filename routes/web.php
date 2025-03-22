<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ModuleLeaderController;
use App\Http\Controllers\TeacherAssistantController;

Auth::routes();

Route::get('/', function () {
if (Auth::check()) {
    $user = Auth::user();
    if ($user->role == 'Admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role == 'Module Leader') {
        return redirect()->route('module_leader.dashboard');
    } elseif ($user->role == 'Teaching Assistant') {
        return redirect()->route('ta.dashboard');
    }
}
    return redirect()->route('login');
});

Route::get('/edit_account', [App\Http\Controllers\TeacherAssistantController::class, 'show'])->name('edit_account');
Route::post('/request_edit_account', [App\Http\Controllers\TeacherAssistantController::class, 'edit'])->name('request_edit_account');
Route::get('/admin/areas_of_knolwedge/show', [App\Http\Controllers\AdminController::class, 'showAreaOfKnowledgeForm'])->name('admin.areas_of_knowledge.show');
Route::post('/admin/areas_of_knolwedge/create', [App\Http\Controllers\AdminController::class, 'createAreaOfKnowledge'])->name('admin.areas_of_knowledge.create');
Route::get('/admin/modules/show', [App\Http\Controllers\AdminController::class, 'showModuleForm'])->name('admin.create_new_module.show');
Route::post('/admin/modules/create', [App\Http\Controllers\AdminController::class, 'createModule'])->name('admin.create_new_module.create');
Route::get('/admin-dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
Route::get('/module-leader-dashboard', [ModuleLeaderController::class, 'index'])->name('module_leader.dashboard');
Route::get('/ta-dashboard', [TeacherAssistantController::class, 'index'])->name('ta.dashboard');
Route::post('/edit_account/approve', [AdminController::class, 'approve'])->name('approve_edit_account');
Route::post('/edit_account/deny', [AdminController::class, 'deny'])->name('deny_edit_account');
Route::get('/create_booking', [ModuleLeaderController::class, 'show'])->name('show_create_booking');
Route::post('/create_booking', [ModuleLeaderController::class, 'store'])->name('create_booking');