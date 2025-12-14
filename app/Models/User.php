<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'account_type',
        'phone_verified',
        'is_active',
        'email_verification_token',
        'phone_verification_token',
        'school_id',
        'department_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token',
        'phone_verification_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'phone_verified' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the login identifier for the user.
     *
     * @return string
     */
    public function getLoginIdentifier()
    {
        return $this->email ?? $this->phone_number;
    }

    /**
     * Get the validation rules for the user.
     *
     * @return array
     */
    public static function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20|unique:users',
            'password' => 'required|string|min:8',
            'account_type' => 'required|string|in:admin,staff,student,parent',
        ];
    }

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
            'email_verification_token' => null,
        ])->save();
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    /**
     * Get the school that this user belongs to.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the department that this user belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the student profile for this user (if account_type is student).
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the students linked to this parent account.
     */
    public function children()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_id', 'student_id')
            ->withPivot('relationship', 'is_primary', 'receive_notifications')
            ->withTimestamps();
    }

    /**
     * Get the parents linked to this student account.
     */
    public function parents()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id')
            ->withPivot('relationship', 'is_primary', 'receive_notifications')
            ->withTimestamps();
    }

    /**
     * Get departments where this user is the head of department.
     */
    public function managedDepartments()
    {
        return $this->hasMany(Department::class, 'head_of_department_id');
    }

    /**
     * Get the courses for the user.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Check if user is a school admin.
     */
    public function isSchoolAdmin(): bool
    {
        return $this->account_type === 'school_admin';
    }

    /**
     * Check if user is a director of studies.
     */
    public function isDirectorOfStudies(): bool
    {
        return $this->account_type === 'director_of_studies';
    }

    /**
     * Check if user is a head of department.
     */
    public function isHeadOfDepartment(): bool
    {
        return $this->account_type === 'head_of_department';
    }

    /**
     * Check if user is a subject teacher.
     */
    public function isSubjectTeacher(): bool
    {
        return $this->account_type === 'subject_teacher';
    }

    /**
     * Check if user is a school staff member (any school role).
     */
    public function isSchoolStaff(): bool
    {
        return in_array($this->account_type, [
            'school_admin',
            'director_of_studies',
            'head_of_department',
            'subject_teacher'
        ]);
    }

    /**
     * Check if user is a super admin (admin without school_id).
     */
    public function isSuperAdmin(): bool
    {
        return $this->account_type === 'admin' && !$this->school_id;
    }

    /**
     * Check if user can manage another user based on role hierarchy.
     * School Admin > Director of Studies > Head of Department > Subject Teacher
     */
    public function canManageUser(User $user): bool
    {
        // Super admin can manage anyone
        if ($this->account_type === 'admin' && !$this->school_id) {
            return true;
        }

        // Users can't manage users from different schools
        if ($this->school_id !== $user->school_id) {
            return false;
        }

        $hierarchy = [
            'school_admin' => 4,
            'director_of_studies' => 3,
            'head_of_department' => 2,
            'subject_teacher' => 1,
        ];

        $currentLevel = $hierarchy[$this->account_type] ?? 0;
        $targetLevel = $hierarchy[$user->account_type] ?? 0;

        return $currentLevel > $targetLevel;
    }

    /**
     * Get the user's active subscription.
     */
    public function activeSubscription()
    {
        return $this->hasOne(\App\Models\UserSubscription::class)
            ->where('is_active', true)
            ->where('end_date', '>=', now());
    }

    public function userSubscriptions()
    {
        return $this->hasMany(\App\Models\UserSubscription::class);
    }

    public function preference()
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * The subjects that the teacher teaches.
     */
    public function subjects()
    {
        return $this->belongsToMany(\App\Models\Subject::class, 'subject_user');
    }

    /**
     * The classes that the teacher teaches.
     */
    public function classes()
    {
        return $this->belongsToMany(\App\Models\SchoolClass::class, 'class_user', 'user_id', 'class_id');
    }

    /**
     * Check if teacher teaches a specific subject (by name or ID).
     */
    public function teachesSubject($subjectNameOrId): bool
    {
        if (!in_array($this->account_type, ['teacher', 'subject_teacher'])) {
            return false;
        }

        // If it's a number, check by ID
        if (is_numeric($subjectNameOrId)) {
            return $this->subjects()->where('subjects.id', $subjectNameOrId)->exists();
        }

        // Check by name (case-insensitive)
        return $this->subjects()->whereRaw('LOWER(subjects.name) = ?', [strtolower($subjectNameOrId)])->exists();
    }

    /**
     * Get all subject names that this teacher teaches.
     */
    public function getTeachingSubjectNames(): array
    {
        return $this->subjects()->pluck('name')->toArray();
    }

    /**
     * The resources created by this teacher.
     */
    public function resources()
    {
        return $this->hasMany(\App\Models\Resource::class, 'teacher_id');
    }

    /**
     * Get the student marks for this user.
     */
    public function marks()
    {
        return $this->hasMany(\App\Models\StudentMark::class);
    }

    /**
     * Chat relationships
     */
    
    /**
     * Get all conversations the user participates in
     */
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot(['joined_at', 'last_read_at', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Get active conversations only
     */
    public function activeConversations()
    {
        return $this->conversations()->wherePivot('is_active', true);
    }

    /**
     * Get all messages sent by the user
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get conversation participants for this user
     */
    public function conversationParticipants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    /**
     * Get conversations created by this user
     */
    public function createdConversations()
    {
        return $this->hasMany(Conversation::class, 'created_by');
    }

    /**
     * Check if user can chat (students only for now)
     */
    public function canChat(): bool
    {
        return $this->account_type === 'student';
    }

    /**
     * Get online status (placeholder for future implementation)
     */
    public function isOnline(): bool
    {
        // This can be implemented later with a last_seen_at field
        return true;
    }


    /**
     * Terminate all existing sessions for this user
     */
    public function terminateAllSessions()
    {
        DB::table('sessions')
            ->where('user_id', $this->id)
            ->delete();
    }

    /**
     * Terminate all sessions except the current one
     */
    public function terminateOtherSessions($currentSessionId = null)
    {
        $query = DB::table('sessions')->where('user_id', $this->id);
        
        if ($currentSessionId) {
            $query->where('id', '!=', $currentSessionId);
        }
        
        $query->delete();
    }

    /**
     * Check if user has any active sessions
     */
    public function hasActiveSessions(): bool
    {
        return DB::table('sessions')
            ->where('user_id', $this->id)
            ->where('last_activity', '>', now()->subMinutes(config('session.lifetime', 120))->timestamp)
            ->exists();
    }

    /**
     * Get the number of active sessions for this user
     */
    public function getActiveSessionCount(): int
    {
        return DB::table('sessions')
            ->where('user_id', $this->id)
            ->where('last_activity', '>', now()->subMinutes(config('session.lifetime', 120))->timestamp)
            ->count();
    }

    /**
     * Get groups created by this user
     */
    public function createdGroups()
    {
        return $this->hasMany(Group::class, 'created_by');
    }

    /**
     * Get groups where this user is a member
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members')
                    ->withPivot('role', 'status', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Get approved groups where this user is a member
     */
    public function approvedGroups()
    {
        return $this->groups()->wherePivot('status', 'approved');
    }

    /**
     * Get projects created by this user
     */
    public function createdProjects()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    /**
     * Get projects where this user is a group member
     */
    public function projects()
    {
        return $this->hasManyThrough(Project::class, Group::class, 'id', 'group_id', 'id', 'id')
                    ->join('group_members', 'groups.id', '=', 'group_members.group_id')
                    ->where('group_members.user_id', $this->id)
                    ->where('group_members.status', 'approved')
                    ->select('projects.*');
    }
}
