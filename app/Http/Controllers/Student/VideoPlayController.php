<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VideoPlayHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Resource;

class VideoPlayController extends Controller
{
    /**
     * Track video play.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function track(Request $request)
    {
        Log::info('Video tracking request received', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        try {
            $request->validate([
                'resource_id' => 'required|exists:resources,id',
                'watch_duration' => 'required|integer|min:0',
                'completed' => 'required|boolean'
            ]);

            $user = Auth::user();
            $resource = Resource::findOrFail($request->resource_id);

            // Validate that the student can access this resource
            if (!$this->canStudentAccessResource($user, $resource)) {
                Log::warning('Unauthorized video access attempt', [
                    'user_id' => $user->id,
                    'resource_id' => $resource->id,
                    'user_level' => $this->getStudentGradeLevel($user),
                    'resource_level' => $resource->grade_level
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to access this resource.'
                ], 403);
            }

            $history = VideoPlayHistory::create([
                'user_id' => Auth::id(),
                'resource_id' => $request->resource_id,
                'played_at' => now(),
                'watch_duration' => $request->watch_duration,
                'completed' => $request->completed
            ]);

            Log::info('Video tracking record created', [
                'history_id' => $history->id,
                'user_id' => $history->user_id,
                'resource_id' => $history->resource_id,
                'watch_duration' => $history->watch_duration,
                'completed' => $history->completed
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Video play tracked successfully',
                'data' => $history
            ]);
        } catch (\Exception $e) {
            Log::error('Error tracking video', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error tracking video: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if a student can access a specific resource
     */
    protected function canStudentAccessResource($user, $resource)
    {
        // Check if resource is assigned to student's school
        $isAssignedToSchool = $resource->schools()
            ->where('schools.id', $user->school_id)
            ->exists();

        if (!$isAssignedToSchool) {
            return false;
        }

        // Get student's grade level
        $studentLevel = $this->getStudentGradeLevel($user);

        // Check if resource grade level matches student's level
        return $resource->grade_level === $studentLevel;
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
