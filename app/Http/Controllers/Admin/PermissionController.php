<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     */
    public function index()
    {
        $permissions = Permission::paginate(10);
        
        return Inertia::render('admin/Permissions/Index', [
            'permissions' => $permissions,
            'title' => 'Manajemen Izin',
        ]);
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return Inertia::render('admin/Permissions/Create', [
            'title' => 'Tambah Izin',
        ]);
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('message', 'Permission berhasil dibuat.');
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');
        
        return Inertia::render('admin/Permissions/Show', [
            'permission' => $permission,
            'title' => 'Detail Izin',
        ]);
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        return Inertia::render('admin/Permissions/Edit', [
            'permission' => $permission,
            'title' => 'Edit Izin',
        ]);
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,'.$permission->id,
        ]);

        $permission->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('message', 'Permission berhasil diperbarui.');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission)
    {
        // Cek apakah permission ini digunakan oleh role manapun
        if ($permission->roles()->count() > 0) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'Permission ini sedang digunakan oleh beberapa role dan tidak dapat dihapus.');
        }
        
        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('message', 'Permission berhasil dihapus.');
    }
}
