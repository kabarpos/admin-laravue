<?php

use App\Http\Controllers\Admin\EmailSettingController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/', function () {
        return inertia('admin/Dashboard/Index');
    })->name('dashboard');

    // Manajemen User
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update-status');
    
    // Manajemen Role
    Route::resource('roles', RoleController::class);
    
    // Manajemen Permission
    Route::resource('permissions', PermissionController::class);
    
    // Pengaturan Email
    Route::get('email-settings', [EmailSettingController::class, 'edit'])->name('email-settings.edit');
    Route::put('email-settings', [EmailSettingController::class, 'update'])->name('email-settings.update');
    Route::post('email-settings/test', [EmailSettingController::class, 'sendTestEmail'])->name('email-settings.test');
});
