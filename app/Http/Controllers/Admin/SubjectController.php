<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    private function getSubjectLevels(): array
    {
        return [
            'O Level' => 'O Level (UCE)',
            'A Level' => 'A Level (UACE)',
            'Both' => 'Both (shared)',
        ];
    }

    private function sanitizePapers(array $papers): array
    {
        return collect($papers)
            ->map(function ($paper) {
                $name = trim($paper['name'] ?? '');
                $code = trim($paper['code'] ?? '');
                $description = trim($paper['description'] ?? '');

                if ($name === '') {
                    return null;
                }

                return array_filter([
                    'name' => $name,
                    'code' => $code !== '' ? $code : null,
                    'description' => $description !== '' ? $description : null,
                ], static function ($value) {
                    return !is_null($value);
                });
            })
            ->filter()
            ->values()
            ->toArray();
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // For school admins, show only system subjects (school_id = null)
        // Super admins see all subjects
        $query = Subject::query();
        
        if ($user->school_id) {
            // School admins: only show system subjects (available to all schools)
            $query->withoutGlobalScope('school')->whereNull('school_id');
        }
        // Super admins (no school_id) can see all subjects

        $searchTerm = trim($request->search ?? '');
        $status = $request->status;

        if ($searchTerm !== '' && $status !== '' && $status !== null) {
            // Both filters provided: match either
            $query->where(function($q) use ($searchTerm, $status) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->where('is_active', (int)$status);
            });
        } elseif ($searchTerm !== '') {
            // Only search
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        } elseif ($status !== '' && $status !== null) {
            // Only status
            $query->where('is_active', (int)$status);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $perPage = $request->get('per_page', 10);
        $subjects = $query->latest()->paginate($perPage);
        $subjects->appends($request->query());

        $levels = $this->getSubjectLevels();

        return view('admin.subjects.index', compact('subjects', 'levels'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Allow school admin and director of studies to create subjects
        if ($user->school_id && !$user->isSchoolAdmin() && !$user->isDirectorOfStudies()) {
            abort(403, 'Access denied. Only School Admins and Directors of Studies can create subjects.');
        }
        
        $levels = $this->getSubjectLevels();

        return view('admin.subjects.create', compact('levels'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Allow school admin and director of studies to create subjects
        if ($user->school_id && !$user->isSchoolAdmin() && !$user->isDirectorOfStudies()) {
            abort(403, 'Access denied. Only School Admins and Directors of Studies can create subjects.');
        }
        
        // Generate slug if not provided
        $slug = $request->slug ?? Str::slug($request->name);
        
        // Build unique validation rule for slug per school
        $slugRule = Rule::unique('subjects', 'slug');
        if ($user->school_id) {
            $slugRule->where('school_id', $user->school_id);
        } else {
            $slugRule->whereNull('school_id');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'objectives' => 'nullable|array',
            'prerequisites' => 'nullable|array',
            'duration' => 'nullable|string|max:255',
            'total_topics' => 'nullable|integer',
            'total_resources' => 'nullable|integer',
            'learning_outcomes' => 'nullable|array',
            'assessment_methods' => 'nullable|array',
            'passing_score' => 'nullable|numeric',
            'is_active' => 'boolean',
            'level' => 'required|in:O Level,A Level,Both',
            'paper_count' => 'nullable|integer|min:1|max:10',
            'papers' => 'nullable|array',
            'papers.*.name' => 'nullable|string|max:255',
            'papers.*.code' => 'nullable|string|max:50',
            'papers.*.description' => 'nullable|string|max:500',
        ]);
        $validated['slug'] = $slug;
        $validated['created_by'] = auth()->id();
        
        // Ensure school_id is set for school staff (TenantScope should handle this, but being explicit)
        if ($user->school_id) {
            $validated['school_id'] = $user->school_id;
        }
        
        // Set default values for required fields
        $validated['total_topics'] = $validated['total_topics'] ?? 0;
        $validated['total_resources'] = $validated['total_resources'] ?? 0;
        $validated['passing_score'] = $validated['passing_score'] ?? 60.00;
        $validated['is_active'] = $validated['is_active'] ?? true;
        
        $validated['objectives'] = $request->objectives ? json_encode(array_filter(array_map('trim', $request->objectives))) : null;
        $validated['prerequisites'] = $request->prerequisites ? json_encode(array_filter(array_map('trim', $request->prerequisites))) : null;
        $validated['learning_outcomes'] = $request->learning_outcomes ? json_encode(array_filter(array_map('trim', $request->learning_outcomes))) : null;
        $validated['assessment_methods'] = $request->assessment_methods ? json_encode(array_filter(array_map('trim', $request->assessment_methods))) : null;

        $papers = $this->sanitizePapers($request->input('papers', []));
        if (!empty($papers)) {
            $validated['papers'] = $papers;
            $validated['paper_count'] = count($papers);
        } else {
            $validated['paper_count'] = max(1, (int)($validated['paper_count'] ?? 1));
            $validated['papers'] = null;
        }

        Subject::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Subject created successfully!',
            'redirect' => route('admin.subjects.index')
        ]);
    }

    protected function findSubjectByHash($hash_id)
    {
        $subject = Subject::findByHashId($hash_id);
        if (!$subject) {
            abort(404);
        }
        return $subject;
    }

    public function edit($hash_id)
    {
        $user = Auth::user();
        $subject = $this->findSubjectByHash($hash_id);
        
        // Check if user can edit this subject
        if ($user->school_id && $subject->school_id != $user->school_id) {
            abort(403, 'Access denied. You can only edit subjects from your school.');
        }
        
        // Allow school admin and director of studies to edit subjects
        if ($user->school_id && !$user->isSchoolAdmin() && !$user->isDirectorOfStudies()) {
            abort(403, 'Access denied. Only School Admins and Directors of Studies can edit subjects.');
        }
        
        $levels = $this->getSubjectLevels();

        return view('admin.subjects.edit', compact('subject', 'levels'));
    }

    public function update(Request $request, $hash_id)
    {
        $user = Auth::user();
        $subject = $this->findSubjectByHash($hash_id);
        
        // Check if user can update this subject
        if ($user->school_id && $subject->school_id != $user->school_id) {
            abort(403, 'Access denied. You can only update subjects from your school.');
        }
        
        // Allow school admin and director of studies to update subjects
        if ($user->school_id && !$user->isSchoolAdmin() && !$user->isDirectorOfStudies()) {
            abort(403, 'Access denied. Only School Admins and Directors of Studies can update subjects.');
        }
        
        // Generate slug if not provided
        $slug = $request->slug ?? Str::slug($request->name);
        
        // Build unique validation rule for slug per school (excluding current subject)
        $slugRule = Rule::unique('subjects', 'slug')->ignore($subject->id);
        if ($user->school_id) {
            $slugRule->where('school_id', $user->school_id);
        } else {
            $slugRule->whereNull('school_id');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'objectives' => 'nullable|array',
            'prerequisites' => 'nullable|array',
            'duration' => 'nullable|string|max:255',
            'total_topics' => 'nullable|integer',
            'total_resources' => 'nullable|integer',
            'learning_outcomes' => 'nullable|array',
            'assessment_methods' => 'nullable|array',
            'passing_score' => 'nullable|numeric',
            'is_active' => 'boolean',
            'level' => 'required|in:O Level,A Level,Both',
            'paper_count' => 'nullable|integer|min:1|max:10',
            'papers' => 'nullable|array',
            'papers.*.name' => 'nullable|string|max:255',
            'papers.*.code' => 'nullable|string|max:50',
            'papers.*.description' => 'nullable|string|max:500',
        ]);
        $validated['slug'] = $slug;
        
        // Set default values for required fields if not provided
        if (!isset($validated['total_topics'])) {
            $validated['total_topics'] = $subject->total_topics ?? 0;
        }
        if (!isset($validated['total_resources'])) {
            $validated['total_resources'] = $subject->total_resources ?? 0;
        }
        if (!isset($validated['passing_score'])) {
            $validated['passing_score'] = $subject->passing_score ?? 60.00;
        }
        
        $validated['objectives'] = $request->objectives ? json_encode(array_filter(array_map('trim', $request->objectives))) : null;
        $validated['prerequisites'] = $request->prerequisites ? json_encode(array_filter(array_map('trim', $request->prerequisites))) : null;
        $validated['learning_outcomes'] = $request->learning_outcomes ? json_encode(array_filter(array_map('trim', $request->learning_outcomes))) : null;
        $validated['assessment_methods'] = $request->assessment_methods ? json_encode(array_filter(array_map('trim', $request->assessment_methods))) : null;

        $papers = $this->sanitizePapers($request->input('papers', []));
        if (!empty($papers)) {
            $validated['papers'] = $papers;
            $validated['paper_count'] = count($papers);
        } else {
            $validated['paper_count'] = max(1, (int)($validated['paper_count'] ?? $subject->paper_count ?? 1));
            $validated['papers'] = null;
        }

        $subject->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Subject updated successfully!',
            'redirect' => route('admin.subjects.index')
        ]);
    }

    public function destroy($hash_id)
    {
        $user = Auth::user();
        $subject = $this->findSubjectByHash($hash_id);
        
        // Check if user can delete this subject
        if ($user->school_id && $subject->school_id != $user->school_id) {
            abort(403, 'Access denied. You can only delete subjects from your school.');
        }
        
        // Allow school admin and director of studies to delete subjects
        if ($user->school_id && !$user->isSchoolAdmin() && !$user->isDirectorOfStudies()) {
            abort(403, 'Access denied. Only School Admins and Directors of Studies can delete subjects.');
        }
        
        $subject->delete();
        return redirect()->route('admin.subjects.index')->with('success', 'Subject deleted!');
    }
} 