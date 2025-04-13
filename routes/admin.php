<?php

use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\EmailSettingController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/', function () {
        // Mengambil data real untuk dashboard
        $userCount = User::count();
        $roleCount = Role::count();
        $permissionCount = Permission::count();
        $recentUsers = User::latest()->take(5)->get();
        
        // Data aktivitas (dalam contoh ini masih dummy, 
        // bisa diganti dengan model Activity jika ada)
        $activities = [
            [
                'user' => 'Admin',
                'action' => 'menyetujui pendaftaran pengguna baru',
                'time' => '5 menit yang lalu'
            ],
            [
                'user' => 'Admin',
                'action' => 'menambahkan izin baru',
                'time' => '2 jam yang lalu'
            ],
            // Data aktivitas lainnya...
        ];
        
        return inertia('admin/Dashboard/Index', [
            'stats' => [
                [
                    'title' => 'Total Pengguna',
                    'value' => $userCount,
                    'icon' => 'Users',
                    'change' => '+12%',
                    'trend' => 'up',
                ],
                [
                    'title' => 'Peran',
                    'value' => $roleCount,
                    'icon' => 'Shield',
                    'change' => '0%',
                    'trend' => 'neutral',
                ],
                [
                    'title' => 'Izin',
                    'value' => $permissionCount,
                    'icon' => 'Key',
                    'change' => '+5',
                    'trend' => 'up',
                ],
                [
                    'title' => 'Login Minggu Ini',
                    'value' => '38',
                    'icon' => 'BarChart3',
                    'change' => '+24%',
                    'trend' => 'up',
                ]
            ],
            'activities' => $activities,
            'title' => 'Dashboard Admin'
        ]);
    })->name('dashboard')->middleware('permission:access_admin_dashboard');

    // Manajemen User
    Route::middleware('permission:manage_users')->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update-status');
        Route::post('users/{user}/resend-verification', [UserController::class, 'resendVerification'])->name('users.resend-verification');
        Route::post('users/{user}/mark-verified', [UserController::class, 'markVerified'])->name('users.mark-verified');
    });
    
    // Manajemen Role
    Route::middleware('permission:manage_roles')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::post('roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.permissions.sync');
    });
    
    // Manajemen Permission
    Route::middleware('permission:manage_permissions')->group(function () {
        Route::resource('permissions', PermissionController::class);
    });
    
    // Pengaturan Email dan Website (gabungan)
    Route::middleware('permission:manage_settings')->group(function () {
        // Pengaturan Email
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
});
