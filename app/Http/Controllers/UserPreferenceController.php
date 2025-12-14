<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use App\Models\SchoolClass;
use App\Models\Term;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Resource;
use App\Traits\FiltersByStudentCombination;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserPreferenceController extends Controller
{
    use FiltersByStudentCombination;

    public function index()
    {
        $user = Auth::user();
        $preference = $user->preference;
        $subscription = UserSubscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('end_date', '>=', now())
            ->first();
        $subscriptionPackage = null;
        if ($subscription && $subscription->subscription_package_id) {
            $subscriptionPackage = DB::table('subscription_packages')->where('id', $subscription->subscription_package_id)->first();
        }
        $classes = \App\Models\SchoolClass::all();
        $subjects = collect();
        $topics = collect();
        $terms = \App\Models\Term::all();

        // If we have a class_id in the URL, use it to load subjects
        if (request('class_id')) {
            $subjects = \App\Models\Subject::whereHas('resources', function($query) {
                $query->where('class_id', request('class_id'))
                    ->whereNotNull('google_drive_link');
            })->get();
        }
        // If we have saved preferences, use them to load subjects
        elseif ($preference && $preference->class_id) {
            $subjects = \App\Models\Subject::whereHas('resources', function($query) use ($preference) {
                $query->where('class_id', $preference->class_id)
                    ->whereNotNull('google_drive_link');
            })->get();
        }

        // If we have a subject_id in the URL, use it to load topics
        if (request('subject_id')) {
            $topics = \App\Models\Topic::whereHas('resources', function($query) {
                $query->where('class_id', request('class_id'))
                    ->where('subject_id', request('subject_id'))
                    ->whereNotNull('google_drive_link');
            })->get();
        }
        // If we have saved preferences, use them to load topics
        elseif ($preference && $preference->subject_id) {
            $topics = \App\Models\Topic::whereHas('resources', function($query) use ($preference) {
                $query->where('class_id', $preference->class_id)
                    ->where('subject_id', $preference->subject_id)
                    ->whereNotNull('google_drive_link');
            })->get();
        }

        return view('student.preferences', compact('preference', 'subscriptionPackage', 'classes', 'subjects', 'topics', 'terms'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Get current active subscription (license)
        $subscription = UserSubscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('end_date', '>=', now())
            ->first();
            
        $subscriptionPackage = null;
        
        if ($subscription && $subscription->subscription_package_id) {
            $subscriptionPackage = DB::table('subscription_packages')->where('id', $subscription->subscription_package_id)->first();
        }

        if (!$subscription || !$subscriptionPackage) {
            return redirect()->route('subscription.index')->with('error', 'Please subscribe to access this feature.');
        }

        $preference = $user->preference ?? new UserPreference(['user_id' => $user->id]);

        // Check if user has exceeded their preference change limit
        if ($preference->exists && $preference->preference_changes_count >= $subscriptionPackage->maximum_active_sessions) {
            return back()->with('error', 'You have reached the maximum number of preference changes allowed for your subscription. Please contact support if you need to make further changes.');
        }

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required_if:subscription_type,term|exists:terms,id',
            'subject_id' => 'required_if:subscription_type,subject,topic|exists:subjects,id',
            'topic_id' => 'required_if:subscription_type,topic|exists:topics,id',
        ]);

        // Verify that the selected resources exist
        if ($subscriptionPackage->subscription_type === 'term') {
            $hasResources = Resource::where('class_id', $validated['class_id'])
                ->where('term_id', $validated['term_id'])
                ->exists();
            
            if (!$hasResources) {
                return back()->with('error', 'No resources available for the selected class and term.');
            }
        } elseif ($subscriptionPackage->subscription_type === 'subject') {
            $hasResources = Resource::where('class_id', $validated['class_id'])
                ->where('subject_id', $validated['subject_id'])
                ->exists();
            
            if (!$hasResources) {
                return back()->with('error', 'No resources available for the selected class and subject.');
            }
        } elseif ($subscriptionPackage->subscription_type === 'topic') {
            $hasResources = Resource::where('class_id', $validated['class_id'])
                ->where('subject_id', $validated['subject_id'])
                ->where('topic_id', $validated['topic_id'])
                ->exists();
            
            if (!$hasResources) {
                return back()->with('error', 'No resources available for the selected class, subject, and topic.');
            }
        }

        $preference->fill($validated);
        $preference->preference_changes_count++;
        $preference->last_preference_change = now();
        $preference->save();

        return redirect()->route('student.preferences.index')->with('success', 'Preferences updated successfully.');
    }

    public function getSubjects(Request $request)
    {
        $classId = $request->input('class_id');
        $termId = $request->input('term_id');

        $query = Subject::whereHas('resources', function($query) use ($classId) {
            $query->where('class_id', $classId);
        });

        if ($termId) {
            $query->whereHas('resources', function($query) use ($termId) {
                $query->where('term_id', $termId);
            });
        }

        return response()->json($query->get());
    }

    public function getTopics(Request $request)
    {
        $classId = $request->input('class_id');
        $subjectId = $request->input('subject_id');
        $termId = $request->input('term_id');

        $query = Topic::whereHas('resources', function($query) use ($classId, $subjectId) {
            $query->where('class_id', $classId)
                  ->where('subject_id', $subjectId);
        });

        if ($termId) {
            $query->whereHas('resources', function($query) use ($termId) {
                $query->where('term_id', $termId);
            });
        }

        return response()->json($query->get());
    }

    public function myVideos(Request $request)
    {
        $user = Auth::user();
        $isTeacher = in_array($user->account_type, ['teacher', 'subject_teacher']);
        
        if ($isTeacher) {
            // For teachers, show only videos they created
            $query = \App\Models\Resource::with(['subject', 'term', 'topic', 'classRoom'])
                ->where('teacher_id', $user->id)
                ->whereNotNull('google_drive_link');
            
            // Add filter for videos with student comments
            if ($request->has('filter')) {
                if ($request->filter === 'unreplied_comments') {
                    // Videos with student comments that teacher hasn't replied to
                    $query->whereHas('comments', function($commentQuery) use ($user) {
                        $commentQuery->whereHas('user', function($userQuery) {
                            $userQuery->where('account_type', 'student');
                        })
                        ->whereNull('parent_id')
                        ->whereDoesntHave('replies', function($replyQuery) use ($user) {
                            $replyQuery->whereHas('user', function($userQuery) use ($user) {
                                $userQuery->where('id', $user->id);
                            });
                        });
                    });
                } elseif ($request->filter === 'replied_comments') {
                    // Videos with student comments that teacher has replied to
                    $query->whereHas('comments', function($commentQuery) use ($user) {
                        $commentQuery->whereHas('user', function($userQuery) {
                            $userQuery->where('account_type', 'student');
                        })
                        ->whereNull('parent_id')
                        ->whereHas('replies', function($replyQuery) use ($user) {
                            $replyQuery->whereHas('user', function($userQuery) use ($user) {
                                $userQuery->where('id', $user->id);
                            });
                        });
                    });
                }
            }
            
            $resources = $query->latest()->paginate(12);
            
            // Add comment counts to each resource
            $resources->getCollection()->transform(function($resource) use ($user) {
                $resource->unreplied_comments_count = $resource->getUnrepliedStudentCommentsCount($user->id);
                $resource->replied_comments_count = $resource->getRepliedStudentCommentsCount($user->id);
                return $resource;
            });
            
            $classes = collect();
            $subjects = collect();
            $topics = collect();
            $terms = collect();
            $isSchoolStudent = false; // Teachers are not school students
            
            return view('student.my-videos', compact('resources', 'classes', 'subjects', 'topics', 'terms', 'isSchoolStudent'));
        }
        
        // Check if user is a school student - they get free access to their school's resources
        // School students have account_type 'student' and a non-null school_id
        $isSchoolStudent = $user->account_type === 'student' && !is_null($user->school_id) && $user->school_id > 0;
        
        // Log for debugging (remove in production)
        if ($user->account_type === 'student') {
            \Log::info('My Videos Access Check', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'account_type' => $user->account_type,
                'school_id' => $user->school_id,
                'is_school_student' => $isSchoolStudent
            ]);
        }
        
        // School students don't need preferences or subscriptions - they get free access
        if ($isSchoolStudent) {
            // Get student's class directly from database - simple and reliable
            $studentClassId = \DB::table('class_user')
                ->where('user_id', $user->id)
                ->value('class_id');
            
            $studentClass = null;
            if ($studentClassId) {
                $studentClass = \App\Models\SchoolClass::find($studentClassId);
            }
            
            // School students see videos assigned via direct school_id OR via pivot table
            $query = \App\Models\Resource::with(['subject', 'term', 'topic', 'classRoom'])
                ->where('is_active', true)
                ->whereNotNull('google_drive_link')
                ->where(function($q) use ($user) {
                    $q->where('school_id', $user->school_id)
                      ->orWhereHas('schools', function($subQuery) use ($user) {
                          $subQuery->where('schools.id', $user->school_id);
                      });
                });
            
            // Filter by student's class - show only videos for their class
            if ($studentClassId) {
                $query->where('class_id', $studentClassId);
            }
            
            // For A Level students (Form 5-6), filter by their subject combination
            if ($studentClass && preg_match('/form\s*[5-6]|s[5-6]/i', strtolower($studentClass->name))) {
                $combinationSubjects = $this->getStudentCombinationSubjects($user);
                if ($combinationSubjects !== null && count($combinationSubjects) > 0) {
                    $query->whereIn('subject_id', $combinationSubjects);
                }
            }
            
            // Apply manual filters if selected
            if ($request->filled('subject_id')) {
                $query->where('subject_id', $request->subject_id);
            }
            if ($request->filled('topic_id')) {
                $query->where('topic_id', $request->topic_id);
            }
            if ($request->filled('term_id')) {
                $query->where('term_id', $request->term_id);
            }
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            }
            
            $resources = $query->latest()->paginate(12);
            $resources->appends($request->query());
            
            // Get filter options from school's resources
            $baseQuery = \App\Models\Resource::where('is_active', true)
                ->where(function($q) use ($user) {
                    $q->where('school_id', $user->school_id)
                      ->orWhereHas('schools', function($subQuery) use ($user) {
                          $subQuery->where('schools.id', $user->school_id);
                      });
                });
            
            $subjects = \App\Models\Subject::whereIn('id', 
                $baseQuery->pluck('subject_id')->unique()->filter()
            )->get();
            
            $topics = \App\Models\Topic::whereIn('id',
                $baseQuery->pluck('topic_id')->unique()->filter()
            )->get();
            
            $terms = \App\Models\Term::whereIn('id',
                $baseQuery->pluck('term_id')->unique()->filter()
            )->get();
            
            $classes = collect();
        } else {
            // For non-school students, check preferences and subscription
            $preference = $user->preference;
            
            // Check if preferences are set
            if (!$preference) {
                return redirect()->route('student.preferences.index')
                    ->with('error', 'Please set your learning preferences before accessing your videos.');
            }

            $subscription = UserSubscription::where('user_id', $user->id)
                ->where('is_active', true)
                ->where('end_date', '>=', now())
                ->first();
            
            // Check if user has no subscription
            if (!$subscription) {
                return redirect()->route('pricing')->with('error', 'Please subscribe to access this feature.');
            }
            
            $subscriptionPackage = null;
            if ($subscription && $subscription->subscription_package_id) {
                $subscriptionPackage = DB::table('subscription_packages')->where('id', $subscription->subscription_package_id)->first();
            }
            
            // Initialize collections
            $resources = collect();
            $classes = collect();
            $subjects = collect();
            $topics = collect();
            $terms = collect();
            
            if ($subscriptionPackage) {
                // Regular subscription-based access
                $query = \App\Models\Resource::with(['subject', 'term', 'topic', 'classRoom'])
                    ->whereNotNull('google_drive_link');
                // Restrict to user's preference
                if ($subscriptionPackage->subscription_type === 'term') {
                    if ($preference->class_id && $preference->term_id) {
                        $query->where('class_id', $preference->class_id)
                            ->where('term_id', $preference->term_id);
                    } else {
                        return redirect()->route('student.preferences.index')
                            ->with('error', 'Please set your class and term preferences before accessing your videos.');
                    }
                } elseif ($subscriptionPackage->subscription_type === 'subject') {
                    if ($preference->class_id && $preference->subject_id) {
                        $query->where('class_id', $preference->class_id)
                            ->where('subject_id', $preference->subject_id);
                    } else {
                        return redirect()->route('student.preferences.index')
                            ->with('error', 'Please set your class and subject preferences before accessing your videos.');
                    }
                } elseif ($subscriptionPackage->subscription_type === 'topic') {
                    if ($preference->class_id && $preference->subject_id && $preference->topic_id) {
                        $query->where('class_id', $preference->class_id)
                            ->where('subject_id', $preference->subject_id)
                            ->where('topic_id', $preference->topic_id);
                    } else {
                        return redirect()->route('student.preferences.index')
                            ->with('error', 'Please set your class, subject, and topic preferences before accessing your videos.');
                    }
                }
                // Apply filters
                if ($query) {
                    if ($request->filled('grade_level')) {
                        $query->where('grade_level', $request->grade_level);
                    }
                    if ($request->filled('subject_id')) {
                        $query->where('subject_id', $request->subject_id);
                    }
                    if ($request->filled('topic_id')) {
                        $query->where('topic_id', $request->topic_id);
                    }
                    if ($request->filled('term_id')) {
                        $query->where('term_id', $request->term_id);
                    }
                    if ($request->filled('class_id')) {
                        $query->where('class_id', $request->class_id);
                    }
                    if ($request->filled('search')) {
                        $searchTerm = $request->search;
                        $query->where(function($q) use ($searchTerm) {
                            $q->where('title', 'like', '%' . $searchTerm . '%')
                              ->orWhere('description', 'like', '%' . $searchTerm . '%')
                              ->orWhere('tags', 'like', '%' . $searchTerm . '%');
                        });
                    }
                    $resources = $query->latest()->paginate(12);
                    $resources->appends($request->query());
                    // Get unique IDs from the filtered resources
                    $allResourceIds = $query->pluck('id');
                    $classIds = \App\Models\Resource::whereIn('id', $allResourceIds)->pluck('class_id')->unique()->filter();
                    $subjectIds = \App\Models\Resource::whereIn('id', $allResourceIds)->pluck('subject_id')->unique()->filter();
                    $topicIds = \App\Models\Resource::whereIn('id', $allResourceIds)->pluck('topic_id')->unique()->filter();
                    $termIds = \App\Models\Resource::whereIn('id', $allResourceIds)->pluck('term_id')->unique()->filter();
                    $classes = \App\Models\SchoolClass::whereIn('id', $classIds)->get();
                    $subjects = \App\Models\Subject::whereIn('id', $subjectIds)->get();
                    $topics = \App\Models\Topic::whereIn('id', $topicIds)->get();
                    $terms = \App\Models\Term::whereIn('id', $termIds)->get();
                }
            }
        }
        return view('student.my-videos', compact('resources', 'classes', 'subjects', 'topics', 'terms', 'isSchoolStudent'));
    }

    public function showMyVideo($id)
    {
        $user = Auth::user();
        $resource = \App\Models\Resource::with(['subject', 'term', 'topic', 'classRoom', 'schools'])->findOrFail($id);
        
        // Check if user has access to this resource
        $hasAccess = false;
        
        // School students get free access to their school's resources
        if ($user->account_type === 'student' && $user->school_id) {
            // Check if resource is assigned to student's school via school_id or pivot table
            $hasAccess = $resource->school_id === $user->school_id 
                || $resource->schools->contains('id', $user->school_id)
                || (!$resource->school_id && $resource->schools->isEmpty()); // Global resources
        } else {
            // Check subscription-based access (existing logic)
            $subscription = \App\Models\UserSubscription::where('user_id', $user->id)
                ->where('end_date', '>', now())
                ->where('is_active', true)
                ->first();
            
            if ($subscription) {
                // User has active subscription - check if resource matches their preferences
                $preference = $user->preference;
                if ($preference) {
                    $hasAccess = true; // Simplified - can add more checks if needed
                }
            }
        }
        
        if (!$hasAccess) {
            abort(403, 'You do not have access to this resource.');
        }
        
        // Only allow if resource has a google_drive_link
        if (!$resource->google_drive_link) {
            abort(404);
        }
        
        // Extract Google Drive file ID
        $driveFileId = null;
        if (preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $resource->google_drive_link, $matches)) {
            $driveFileId = $matches[1];
        }
        return view('student.my-videos-show', compact('resource', 'driveFileId'));
    }

    public function uploadAssessment(Request $request, $id)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if ($user->account_type !== 'teacher') {
            return response()->json(['success' => false, 'message' => 'Only teachers can upload assessment tests.'], 403);
        }
        
        $resource = \App\Models\Resource::findOrFail($id);
        
        // Check if the teacher owns this resource
        if ($resource->teacher_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'You can only upload assessment tests for your own videos.'], 403);
        }
        
        // Validate the request
        $validated = $request->validate([
            'assessment_tests' => 'required|file|mimes:pdf|max:10240'
        ]);
        
        try {
            // Handle file upload
            if ($request->hasFile('assessment_tests')) {
                $file = $request->file('assessment_tests');
                $path = $file->store('resources/assessments', 'public');
                
                // Update the resource
                $resource->assessment_tests_path = $path;
                $resource->assessment_tests_type = $file->getClientOriginalExtension();
                $resource->save();
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Assessment test uploaded successfully!'
                ]);
            }
            
            return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }

    public function uploadNotes(Request $request, $id)
    {
        $user = Auth::user();
        
        // Check if user is a teacher
        if ($user->account_type !== 'teacher') {
            return response()->json(['success' => false, 'message' => 'Only teachers can upload study materials.'], 403);
        }
        
        $resource = \App\Models\Resource::findOrFail($id);
        
        // Check if the teacher owns this resource
        if ($resource->teacher_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'You can only upload study materials for your own videos.'], 403);
        }
        
        // Validate the request
        $validated = $request->validate([
            'notes_file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240'
        ]);
        
        try {
            // Handle file upload
            if ($request->hasFile('notes_file')) {
                $file = $request->file('notes_file');
                $path = $file->store('resources/notes', 'public');
                
                // Update the resource
                $resource->notes_file_path = $path;
                $resource->notes_file_type = $file->getClientOriginalExtension();
                $resource->save();
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Study material uploaded successfully!'
                ]);
            }
            
            return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }

    public function uploadAssignment(Request $request, $id)
    {
        $user = Auth::user();
        
        // Check if user is a student
        if ($user->account_type !== 'student') {
            return response()->json(['success' => false, 'message' => 'Only students can submit assignments.'], 403);
        }
        
        $resource = \App\Models\Resource::findOrFail($id);
        
        // Check if the resource has an assessment test
        if (!$resource->assessment_tests_path) {
            return response()->json(['success' => false, 'message' => 'No assessment test available for this resource.'], 400);
        }
        
        // Check if student has already submitted an assignment
        $existingAssignment = \App\Models\StudentAssignment::where('student_id', $user->id)
            ->where('resource_id', $resource->id)
            ->first();
            
        if ($existingAssignment) {
            return response()->json(['success' => false, 'message' => 'You have already submitted an assignment for this assessment.'], 400);
        }
        
        // Validate the request
        $validated = $request->validate([
            'assignment_file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:20480'
        ]);
        
        try {
            // Handle file upload
            if ($request->hasFile('assignment_file')) {
                $file = $request->file('assignment_file');
                $path = $file->store('student-assignments', 'public');
                
                // Create assignment record
                $assignment = \App\Models\StudentAssignment::create([
                    'student_id' => $user->id,
                    'resource_id' => $resource->id,
                    'assignment_file_path' => $path,
                    'assignment_file_type' => $file->getClientOriginalExtension(),
                    'status' => 'submitted',
                    'submitted_at' => now()
                ]);
                
                // Create notification for teacher
                if ($resource->teacher_id) {
                    \App\Models\Notification::create([
                        'user_id' => $resource->teacher_id,
                        'resource_id' => $resource->id,
                        'type' => 'assignment_submitted',
                        'title' => 'New Assignment Submission',
                        'message' => $user->name . ' submitted an assignment for "' . $resource->title . '"',
                    ]);
                }
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Assignment submitted successfully! Your work is now under review.'
                ]);
            }
            
            return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Download student's own submitted assignment
     */
    public function downloadStudentAssignment($id)
    {
        $user = Auth::user();
        
        // Check if user is a student
        if ($user->account_type !== 'student') {
            abort(403, 'Access denied. Only students can download their own assignments.');
        }

        $assignment = \App\Models\StudentAssignment::with('resource')
            ->where('student_id', $user->id)
            ->findOrFail($id);

        $filePath = storage_path('app/public/' . $assignment->assignment_file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'Assignment file not found.');
        }

        $fileName = 'My_Assignment_' . $assignment->resource->title . '_' . $assignment->submitted_at->format('Y-m-d') . '.' . $assignment->assignment_file_type;
        
        return response()->download($filePath, $fileName);
    }

    /**
     * Download assessment report for a specific assignment
     */
    public function downloadAssessmentReport($assignmentId)
    {
        $user = Auth::user();
        
        // Check if user is a student
        if ($user->account_type !== 'student') {
            abort(403, 'Access denied. Only students can download their assessment reports.');
        }

        $assignment = \App\Models\StudentAssignment::with(['resource.subject', 'resource.term', 'resource.teacher'])
            ->where('student_id', $user->id)
            ->findOrFail($assignmentId);

        // Generate HTML report
        $html = view('student.assessment-report', compact('assignment'))->render();
        
        $fileName = 'Assessment_Report_' . $assignment->resource->title . '_' . $assignment->submitted_at->format('Y-m-d') . '.html';
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Download PDF of best 3 graded assignments
     */
    public function downloadBestThreeAssignments()
    {
        $user = Auth::user();
        
        // Check if user is a student
        if ($user->account_type !== 'student') {
            abort(403, 'Access denied. Only students can download their assignments.');
        }

        // Get top 3 graded assignments (best scores first)
        $bestAssignments = \App\Models\StudentAssignment::with([
            'resource.subject', 
            'resource.term', 
            'resource.topic',
            'resource.teacher'
        ])
            ->where('student_id', $user->id)
            ->whereNotNull('grade')
            ->orderBy('grade', 'desc')
            ->orderBy('submitted_at', 'desc')
            ->take(3)
            ->get();

        if ($bestAssignments->isEmpty()) {
            return redirect()->route('student.my-assignments.index')
                ->with('error', 'You do not have any graded assignments yet.');
        }

        // Generate PDF using DomPDF
        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.best-three-assignments-pdf', [
                'assignments' => $bestAssignments,
                'user' => $user,
                'school' => $user->school,
            ]);

            $fileName = 'Best_3_Assignments_' . str_replace(' ', '_', $user->name) . '_' . now()->format('Y-m-d') . '.pdf';
            
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return redirect()->route('student.my-assignments.index')
                ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Display all assignments submitted by the student
     */
    public function myAssignments(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is a student
        if ($user->account_type !== 'student') {
            abort(403, 'Access denied. Only students can view their assignments.');
        }

        $query = \App\Models\StudentAssignment::with(['resource.subject', 'resource.term', 'resource.topic'])
            ->where('student_id', $user->id);

        // Filter by subject combination for A-Level students (Form 5 & 6)
        $combinationSubjects = $this->getStudentCombinationSubjects($user);
        if ($combinationSubjects !== null) {
            $query->whereHas('resource', function($q) use ($combinationSubjects) {
                $q->whereIn('subject_id', $combinationSubjects);
            });
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('subject_id')) {
            $query->whereHas('resource', function($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }

        if ($request->filled('term_id')) {
            $query->whereHas('resource', function($q) use ($request) {
                $q->where('term_id', $request->term_id);
            });
        }

        if ($request->filled('grade_range')) {
            switch ($request->grade_range) {
                case 'excellent':
                    $query->where('grade', '>=', 90);
                    break;
                case 'good':
                    $query->whereBetween('grade', [80, 89]);
                    break;
                case 'average':
                    $query->whereBetween('grade', [70, 79]);
                    break;
                case 'below_average':
                    $query->whereBetween('grade', [60, 69]);
                    break;
                case 'poor':
                    $query->where('grade', '<', 60);
                    break;
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        // Order by grade (best scores first), then by submitted date for ungraded assignments
        // For MySQL: NULLs are put last, then sorted by grade descending, then by submission date
        $assignments = $query->orderByRaw('grade IS NULL') // Put NULL grades last (0 for non-null, 1 for null)
            ->orderBy('grade', 'desc') // Highest grades first
            ->orderBy('submitted_at', 'desc') // Most recent first for tie-breaking
            ->paginate(15);

        // Get filter options
        $subjects = \App\Models\Subject::where('is_active', true)->orderBy('name')->get();
        $terms = \App\Models\Term::orderBy('name')->get();

        return view('student.my-assignments', compact('assignments', 'subjects', 'terms'));
    }
}
