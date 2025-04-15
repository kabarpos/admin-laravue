<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        // Ambil peran dengan permission terkait dan hitung jumlah pengguna
        $roles = Role::with('permissions')
            ->withCount('users')
            ->paginate(10);
        
        return Inertia::render('admin/Roles/Index', [
            'roles' => $roles,
            'title' => 'Manajemen Peran',
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::all();
        
        return Inertia::render('admin/Roles/Create', [
            'permissions' => $permissions,
            'title' => 'Tambah Peran Baru',
        ]);
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'sometimes|array',
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        if (isset($validated['permissions']) && !empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('admin.roles.index')
            ->with('message', 'Peran berhasil dibuat.');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        $role->loadCount('users');
        
        return Inertia::render('admin/Roles/Show', [
            'role' => $role,
            'title' => 'Detail Peran',
        ]);
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::all();
        
        return Inertia::render('admin/Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
            'title' => 'Edit Peran',
        ]);
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'permissions' => 'sometimes|array',
        ]);

        $role->update(['name' => $validated['name']]);
        
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('admin.roles.index')
            ->with('message', 'Peran berhasil diperbarui.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting key roles like admin, super-admin, etc.
        if (in_array($role->name, ['admin', 'super-admin'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Peran ini tidak dapat dihapus karena merupakan peran sistem.');
        }
        
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('message', 'Peran berhasil dihapus.');
    }
    
    /**
     * Sync permissions for a role
     */
    public function syncPermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        $role->syncPermissions($validated['permissions']);
        
        return redirect()->route('admin.roles.show', $role)
            ->with('message', 'Perizinan peran berhasil diperbarui.');
    }
}
