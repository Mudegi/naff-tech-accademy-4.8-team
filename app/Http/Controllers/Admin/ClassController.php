<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    private function getLevelOptions(): array
    {
        return [
            'O Level' => 'O Level (Form 1 - Form 4)',
            'A Level' => 'A Level (Form 5 - Form 6)',
        ];
    }

    public function index(Request $request)
    {
        // Show only system classes (Form 1-6) - schools cannot create their own classes
        $query = SchoolClass::withoutGlobalScope('school')->where('is_system_class', true);

        $searchTerm = trim($request->search ?? '');
        $status = $request->status;

        if ($searchTerm !== '' && $status !== '' && $status !== null) {
            $query->where(function($q) use ($searchTerm, $status) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->where('is_active', (int)$status);
            });
        } elseif ($searchTerm !== '') {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        } elseif ($status !== '' && $status !== null) {
            $query->where('is_active', (int)$status);
        }

        $perPage = $request->get('per_page', 10);
        $classes = $query->with('subjects')->latest()->paginate($perPage);
        $classes->appends($request->query());

        return view('admin.classes.index', compact('classes'));
    }

    // Classes are system-wide only (Form 1-6, Senior 1-6)
    // Schools cannot create custom classes - they can only use the predefined system classes
    
    public function create()
    {
        abort(403, 'Classes are system-wide. Schools cannot create custom classes.');
    }

    public function store(Request $request)
    {
        abort(403, 'Classes are system-wide. Schools cannot create custom classes.');
    }

    public function edit(SchoolClass $class)
    {
        abort(403, 'Classes are system-wide. Schools cannot edit classes.');
    }

    public function update(Request $request, SchoolClass $class)
    {
        abort(403, 'Classes are system-wide. Schools cannot edit classes.');
    }

    public function destroy(SchoolClass $class)
    {
        abort(403, 'Classes are system-wide. Schools cannot delete classes.');
    }
} 