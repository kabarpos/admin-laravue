<?php

use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\EmailSettingController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
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
    Route::post('users/{user}/resend-verification', [UserController::class, 'resendVerification'])->name('users.resend-verification');
    Route::post('users/{user}/mark-verified', [UserController::class, 'markVerified'])->name('users.mark-verified');
    
    // Manajemen Role
    Route::resource('roles', RoleController::class);
    Route::post('roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.permissions.sync');
    
    // Manajemen Permission
    Route::resource('permissions', PermissionController::class);
    
    // Pengaturan Email (Format Baru)
    Route::get('email', [EmailController::class, 'index'])->name('email.index');
    Route::put('email', [EmailController::class, 'update'])->name('email.update');
    Route::post('email/test', [EmailController::class, 'test'])->name('email.test');
    
    // Pengaturan Website
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings/general', [SettingController::class, 'updateGeneral'])->name('settings.update-general');
    Route::put('settings/footer', [SettingController::class, 'updateFooter'])->name('settings.update-footer');
    Route::put('settings/scripts', [SettingController::class, 'updateScripts'])->name('settings.update-scripts');
    Route::post('settings/logo', [SettingController::class, 'uploadLogo'])->name('settings.upload-logo');
    Route::post('settings/favicon', [SettingController::class, 'uploadFavicon'])->name('settings.upload-favicon');
    Route::post('settings/og-image', [SettingController::class, 'uploadOgImage'])->name('settings.upload-og-image');
    
    // Deprecated - akan dihapus setelah migrasi selesai
    Route::get('email-settings', [EmailSettingController::class, 'edit'])->name('email-settings.edit');
    Route::put('email-settings', [EmailSettingController::class, 'update'])->name('email-settings.update');
    Route::post('email-settings/test', [EmailSettingController::class, 'sendTestEmail'])->name('email-settings.test');
});
