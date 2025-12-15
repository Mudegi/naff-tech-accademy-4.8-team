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
        
        // Determine student's grade level (O Level or A Level)
        $studentLevel = $this->getStudentGradeLevel($user);
        
        if (!$studentLevel) {
            // If we can't determine the level, show no resources
            $resources = collect();
        } else {
            // Build query for resources assigned to the student's school
            // Filter by grade level and school assignment (many-to-many relationship)
            $query = Resource::whereHas('schools', function($q) use ($user) {
                    $q->where('schools.id', $user->school_id);
                })
                ->where('grade_level', $studentLevel)
                ->where('is_active', true)
                ->whereNotNull('google_drive_link')
                ->where('google_drive_link', '!=', '');
            
            // For A-Level students, also filter by their subject combination
            if ($studentLevel === 'A Level') {
                $combinationSubjects = $this->getStudentCombinationSubjects($user);
                if ($combinationSubjects !== null) {
                    $query->whereIn('subject_id', $combinationSubjects);
                }
            }
            
            $resources = $query->with(['subject', 'topic', 'term', 'classRoom', 'teacher'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }
        
        return view('student.resources.index', compact('resources'));
    }

    /**
     * Get the grade level (O Level or A Level) for a student
     */
    protected function getStudentGradeLevel($user)
    {
        // Check from students table
        $student = \DB::table('students')
            ->where('user_id', $user->id)
            ->first(['level', 'class']);

        if (!$student) {
            return null;
        }

        // Determine grade level based on level field or class
        if ($student->level === 'A Level' || in_array($student->class, ['Form 5', 'Form 6'])) {
            return 'A Level';
        } elseif ($student->level === 'O Level' || in_array($student->class, ['Form 1', 'Form 2', 'Form 3', 'Form 4'])) {
            return 'O Level';
        }

        return null;
    }
}
