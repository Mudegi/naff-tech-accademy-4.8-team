<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TopicController extends Controller
{
    public function index(Request $request)
    {
        $query = Topic::query();

        $hasSearch = $request->filled('search');
        $hasStatus = $request->filled('status');
        $hasSubject = $request->filled('subject_id');

        // Log incoming filter values
        Log::info('Filter values', [
            'search' => $request->search,
            'status' => $request->status,
            'subject_id' => $request->subject_id
        ]);

        if ($hasSearch) {
            $searchTerm = trim($request->search);
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }
        if ($hasStatus) {
            $query->where('is_active', $request->status);
        }
        if ($hasSubject) {
            $query->where('subject_id', (int)$request->subject_id);
        }

        // Log the SQL query and bindings
        Log::info('Query', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        $perPage = $request->get('per_page', 10);
        $topics = $query->with('subject')->latest()->paginate($perPage);
        $topics->appends($request->query());
        $subjects = \App\Models\Subject::where('is_active', true)->get();
        return view('admin.topics.index', compact('topics', 'subjects'));
    }

    public function create()
    {
        $subjects = Subject::where('is_active', true)->get();
        return view('admin.topics.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:topics,slug',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['created_by'] = auth()->id();
        Topic::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'Topic created successfully!',
            'redirect' => route('admin.topics.index')
        ]);
    }

    public function edit($hash_id)
    {
        $topic = Topic::findByHashId($hash_id);
        if (!$topic) {
            abort(404);
        }
        $subjects = Subject::where('is_active', true)->get();
        return view('admin.topics.edit', compact('topic', 'subjects'));
    }

    public function update(Request $request, $hash_id)
    {
        $topic = Topic::findByHashId($hash_id);
        if (!$topic) {
            abort(404);
        }
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:topics,slug,' . $topic->id,
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $topic->update($validated);
        return response()->json([
            'success' => true,
            'message' => 'Topic updated successfully!',
            'redirect' => route('admin.topics.index')
        ]);
    }

    public function destroy($hash_id)
    {
        $topic = Topic::findByHashId($hash_id);
        if (!$topic) {
            abort(404);
        }
        $topic->delete();
        return redirect()->route('admin.topics.index')->with('success', 'Topic deleted!');
    }

    public function topicsBySubject($subjectId)
    {
        $topics = \App\Models\Topic::where('subject_id', $subjectId)->where('is_active', true)->get(['id', 'name']);
        return response()->json($topics);
    }
} 