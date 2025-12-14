<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TermsController extends Controller
{
    public function index()
    {
        $terms = DB::table('terms')->orderByDesc('created_at')->get();
        $user = Auth::user();
        $userPermissions = [];
        if ($user) {
            $roleIds = DB::table('role_user')->where('user_id', $user->id)->pluck('role_id');
            $permissionIds = DB::table('permission_role')->whereIn('role_id', $roleIds)->pluck('permission_id');
            $userPermissions = DB::table('permissions')->whereIn('id', $permissionIds)->pluck('name')->toArray();
        }
        return view('admin.terms.index', compact('terms', 'userPermissions'));
    }

    public function create()
    {
        $user = Auth::user();
        $userPermissions = [];
        if ($user) {
            $roleIds = DB::table('role_user')->where('user_id', $user->id)->pluck('role_id');
            $permissionIds = DB::table('permission_role')->whereIn('role_id', $roleIds)->pluck('permission_id');
            $userPermissions = DB::table('permissions')->whereIn('id', $permissionIds)->pluck('name')->toArray();
        }
        return view('admin.terms.create', compact('userPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        DB::table('terms')->insert([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
            'created_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.terms.index')->with('success', 'Term created successfully.');
    }

    public function edit($id)
    {
        $term = DB::table('terms')->where('id', $id)->first();
        if (!$term) {
            abort(404);
        }
        $user = Auth::user();
        $userPermissions = [];
        if ($user) {
            $roleIds = DB::table('role_user')->where('user_id', $user->id)->pluck('role_id');
            $permissionIds = DB::table('permission_role')->whereIn('role_id', $roleIds)->pluck('permission_id');
            $userPermissions = DB::table('permissions')->whereIn('id', $permissionIds)->pluck('name')->toArray();
        }
        return view('admin.terms.edit', compact('term', 'userPermissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        DB::table('terms')->where('id', $id)->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.terms.index')->with('success', 'Term updated successfully.');
    }

    public function show($id)
    {
        $term = DB::table('terms')->where('id', $id)->first();
        if (!$term) {
            abort(404);
        }
        $user = Auth::user();
        $userPermissions = [];
        if ($user) {
            $roleIds = DB::table('role_user')->where('user_id', $user->id)->pluck('role_id');
            $permissionIds = DB::table('permission_role')->whereIn('role_id', $roleIds)->pluck('permission_id');
            $userPermissions = DB::table('permissions')->whereIn('id', $permissionIds)->pluck('name')->toArray();
        }
        return view('admin.terms.show', compact('term', 'userPermissions'));
    }
}
