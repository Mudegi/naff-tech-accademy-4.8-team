<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    private function checkSuperAdmin()
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can manage roles.');
        }
    }

    public function index()
    {
        $this->checkSuperAdmin();
        $roles = DB::table('roles')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->checkSuperAdmin();
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $this->checkSuperAdmin();
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
        ]);
        DB::table('roles')->insert([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $this->checkSuperAdmin();
        $role = DB::table('roles')->where('id', $id)->first();
        $permissions = DB::table('permissions')->get();
        $rolePermissions = DB::table('permission_role')->where('role_id', $id)->pluck('permission_id')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $this->checkSuperAdmin();
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'description' => 'nullable|string',
        ]);
        DB::table('roles')->where('id', $id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'updated_at' => now(),
        ]);
        // Sync permissions
        $permissions = $request->input('permissions', []);
        DB::table('permission_role')->where('role_id', $id)->delete();
        foreach ($permissions as $permissionId) {
            DB::table('permission_role')->insert([
                'role_id' => $id,
                'permission_id' => $permissionId,
            ]);
        }
        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        $this->checkSuperAdmin();
        DB::table('roles')->where('id', $id)->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
} 