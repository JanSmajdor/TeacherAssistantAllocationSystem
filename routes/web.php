<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ModuleLeaderController;
use App\Http\Controllers\TeacherAssistantController;

Auth::routes();

// Redirect based on user role
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

// Routes for Teaching Assistants
Route::middleware(['isTA'])->group(function () {
    Route::get('/edit_account', [TeacherAssistantController::class, 'show'])->name('edit_account');
    Route::post('/request_edit_account', [TeacherAssistantController::class, 'edit'])->name('request_edit_account');
    Route::post('/request_hide', [TeacherAssistantController::class, 'hideRequest'])->name('request_hide');
    Route::get('/ta-dashboard', [TeacherAssistantController::class, 'index'])->name('ta.dashboard');
});

// Routes for Admins
Route::middleware(['isAdmin'])->group(function () {
    Route::get('/admin/areas_of_knolwedge/show', [AdminController::class, 'showAreaOfKnowledgeForm'])->name('admin.areas_of_knowledge.show');
    Route::post('/admin/areas_of_knolwedge/create', [AdminController::class, 'createAreaOfKnowledge'])->name('admin.areas_of_knowledge.create');
    Route::get('/admin/modules/show', [AdminController::class, 'showModuleForm'])->name('admin.create_new_module.show');
    Route::post('/admin/modules/create', [AdminController::class, 'createModule'])->name('admin.create_new_module.create');
    Route::post('/admin/bookings/manual_assign', [AdminController::class, 'manuallyAssignTA'])->name('admin.manually_assign_ta');
    Route::get('/admin-dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/edit_account/approve', [AdminController::class, 'approveEditAccountRequest'])->name('approve_edit_account');
    Route::post('/edit_account/deny', [AdminController::class, 'denyEditAccountRequest'])->name('deny_edit_account');
    Route::post('/booking/accept', [AdminController::class, 'approveBooking'])->name('approve_booking');
    Route::post('/booking/decline', [AdminController::class, 'denyBooking'])->name('deny_booking');
});

// Routes for Module Leaders
Route::middleware(['isModuleLeader'])->group(function () {
    Route::get('/module-leader-dashboard', [ModuleLeaderController::class, 'index'])->name('module_leader.dashboard');
    Route::get('/create_booking', [ModuleLeaderController::class, 'show'])->name('show_create_booking');
    Route::post('/create_booking', [ModuleLeaderController::class, 'store'])->name('create_booking');
});