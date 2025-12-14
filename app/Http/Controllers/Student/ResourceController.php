<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Traits\FiltersByStudentCombination;
use Illuminate\Support\Facades\Auth;

class ResourceController extends Controller
{
    use FiltersByStudentCombination;

    public function index()
    {
        $user = Auth::user();
        
        // Get student's class IDs - bypass global scope
        $classIds = \DB::table('class_user')
            ->where('user_id', $user->id)
            ->pluck('class_id')
            ->toArray();
        
        if (empty($classIds)) {
            $resources = collect();
        } else {
            // Build query for resources
            $query = Resource::whereIn('class_id', $classIds)
                ->where('school_id', $user->school_id)
                ->where('is_active', true);
            
            // Filter by subject combination for A-Level students (Form 5 & 6)
            $combinationSubjects = $this->getStudentCombinationSubjects($user);
            if ($combinationSubjects !== null) {
                $query->whereIn('subject_id', $combinationSubjects);
            }
            
            $resources = $query->with(['subject', 'topic', 'term', 'classRoom', 'teacher'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }
        
        return view('student.resources.index', compact('resources'));
    }
}
