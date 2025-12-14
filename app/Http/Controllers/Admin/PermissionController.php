<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    private function checkSuperAdmin()
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only super administrators can manage permissions.');
        }
    }

    public function index(Request $request)
    {
        $this->checkSuperAdmin();
        $query = DB::table('permissions');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Sort functionality
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $permissions = $query->paginate($perPage);
        $permissions->appends($request->query());

        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        $this->checkSuperAdmin();
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $this->checkSuperAdmin();
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string',
        ]);
        DB::table('permissions')->insert([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function edit($id)
    {
        $this->checkSuperAdmin();
        $permission = DB::table('permissions')->where('id', $id)->first();
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        $this->checkSuperAdmin();
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
            'description' => 'nullable|string',
        ]);
        DB::table('permissions')->where('id', $id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'updated_at' => now(),
        ]);
        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy($id)
    {
        $this->checkSuperAdmin();
        DB::table('permissions')->where('id', $id)->delete();
        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }
} 