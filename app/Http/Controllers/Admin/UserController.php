<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $usersData = User::with('roles')->paginate(10);
        
        return Inertia::render('admin/Users/Index', [
            'users' => [
                'data' => $usersData->items(),
                'links' => $usersData->linkCollection()->toArray(),
                'meta' => [
                    'current_page' => $usersData->currentPage(),
                    'from' => $usersData->firstItem(),
                    'last_page' => $usersData->lastPage(),
                    'path' => $usersData->path(),
                    'per_page' => $usersData->perPage(),
                    'to' => $usersData->lastItem(),
                    'total' => $usersData->total(),
                ],
            ],
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        
        return Inertia::render('admin/Users/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,blocked,rejected',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
        ]);

        if (isset($validated['role_ids']) && !empty($validated['role_ids'])) {
            $user->syncRoles($validated['role_ids']);
        }

        return redirect()->route('admin.users.index')
            ->with('message', 'Pengguna berhasil dibuat.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('roles');
        
        return Inertia::render('admin/Users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Ambil model roles
        $roles = Role::all();
        
        // Ambil array ID peran langsung dari database untuk memastikan data valid
        $userRoles = DB::table('model_has_roles')
                        ->where('model_id', $user->id)
                        ->where('model_type', get_class($user))
                        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                        ->pluck('roles.id')
                        ->toArray();
        
        return Inertia::render('admin/Users/Edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Aturan validasi dasar
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,'.$user->id,
            'status' => 'required|in:active,inactive,blocked,rejected',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
        ];
        
        // Siapkan data untuk update 
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
        ];
        
        // Jika password diisi (tidak kosong), tambahkan validasi dan update password
        if ($request->filled('password') && strlen(trim($request->password)) > 0) {
            $rules['password'] = 'required|string|min:8|confirmed';
            $data['password'] = Hash::make($request->password);
        }
        
        // Validasi data
        $validated = $request->validate($rules);
        
        // Update user (data sudah disiapkan di atas)
        $user->update($data);
        
        // Sync roles jika ada
        if ($request->has('role_ids')) {
            $roleIds = array_map('intval', $request->input('role_ids', []));
            $user->syncRoles($roleIds);
        }
        
        return redirect()->route('admin.users.index')
            ->with('message', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('message', 'Pengguna berhasil dihapus.');
    }

    /**
     * Update the status of a user.
     */
    public function updateStatus(Request $request, User $user)
    {
        \Log::info('Permintaan update status diterima', [
            'user_id' => $user->id,
            'status_requested' => $request->status,
            'current_status' => $user->status,
            'request_data' => $request->all()
        ]);
        
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,blocked,rejected',
        ]);

        $user->update([
            'status' => $validated['status']
        ]);
        
        \Log::info('Status pengguna berhasil diperbarui', [
            'user_id' => $user->id,
            'old_status' => $user->getOriginal('status'),
            'new_status' => $user->status
        ]);

        return redirect()->back()
            ->with('message', 'Status pengguna berhasil diperbarui.');
    }
    
    /**
     * Kirim ulang email verifikasi untuk pengguna
     */
    public function resendVerification(User $user)
    {
        try {
            // Periksa pengaturan untuk melihat apakah verifikasi diaktifkan
            $settings = \App\Models\EmailSetting::getSettings();
            if (!$settings->enable_verification) {
                // Jika fitur verifikasi dinonaktifkan, verifikasi pengguna secara otomatis
                $user->markEmailAsVerified();
                return response()->json(['message' => 'Email otomatis ditandai sebagai terverifikasi karena fitur verifikasi dinonaktifkan']);
            }

            // Buat URL verifikasi
            $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
            );

            // Kirim email verifikasi kustom
            \Illuminate\Support\Facades\Mail::to($user->email)
                ->send(new \App\Mail\CustomVerifyEmail($user, $verificationUrl));

            return response()->json(['message' => 'Email verifikasi berhasil dikirim']);
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email verifikasi', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['message' => 'Gagal mengirim email verifikasi: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Tandai email pengguna sebagai terverifikasi
     */
    public function markVerified(User $user)
    {
        try {
            // Tandai email sebagai terverifikasi
            $user->markEmailAsVerified();
            
            // Pastikan status pengguna aktif jika masih inactive
            if ($user->status === 'inactive') {
                $user->update(['status' => 'active']);
            }
            
            \Log::info('Email pengguna ditandai sebagai terverifikasi', [
                'user_id' => $user->id,
                'admin_id' => auth()->id()
            ]);
            
            return response()->json(['message' => 'Email pengguna berhasil ditandai sebagai terverifikasi']);
        } catch (\Exception $e) {
            \Log::error('Gagal menandai email sebagai terverifikasi', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['message' => 'Gagal menandai email sebagai terverifikasi: ' . $e->getMessage()], 500);
        }
    }
}
