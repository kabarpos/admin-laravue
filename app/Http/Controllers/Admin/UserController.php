<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        
        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        
        return Inertia::render('Admin/Users/Create', [
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
            'role_ids' => 'sometimes|array',
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
        
        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::all();
        
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,'.$user->id,
            'status' => 'required|in:active,inactive,blocked,rejected',
            'role_ids' => 'sometimes|array',
        ]);

        // Password baru opsional saat update
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if (isset($validated['role_ids'])) {
            $user->syncRoles($validated['role_ids']);
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
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,blocked,rejected',
        ]);

        $user->update([
            'status' => $validated['status']
        ]);

        return redirect()->back()
            ->with('message', 'Status pengguna berhasil diperbarui.');
    }
}
