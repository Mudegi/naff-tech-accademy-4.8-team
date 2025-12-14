<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vinkla\Hashids\Facades\Hashids;
use App\Models\Concerns\TenantScope;

class Resource extends Model
{
    use HasFactory, TenantScope;

    protected $fillable = [
        'topic_id',
        'term_id',
        'subject_id',
        'class_id',
        'created_by',
        'teacher_id',
        'grade_level',
        'title',
        'description',
        'video_url',
        'google_drive_link',
        'notes_file_path',
        'notes_file_type',
        'assessment_tests_path',
        'assessment_tests_type',
        'learning_outcomes',
        'tags',
        'is_active',
        'visible_as_sample',
        'school_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'visible_as_sample' => 'boolean'
    ];

    protected $appends = ['tags_array'];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id')->withoutGlobalScope('school');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * The schools that have access to this resource via pivot table.
     */
    public function schools()
    {
        return $this->belongsToMany(School::class, 'resource_school', 'resource_id', 'school_id')
            ->withTimestamps();
    }

    public function getHashIdAttribute()
    {
        return Hashids::encode($this->id);
    }

    public static function findByHashId($hash_id)
    {
        $ids = Hashids::decode($hash_id);
        if (count($ids) === 0) {
            abort(404);
        }
        return static::findOrFail($ids[0]);
    }

    public function getTagsArrayAttribute()
    {
        if (is_string($this->tags)) {
            return array_filter(array_map('trim', explode(',', $this->tags)));
        }
        return $this->tags ?? [];
    }

    public function setTagsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['tags'] = implode(',', array_filter(array_map('trim', $value)));
        } else {
            $this->attributes['tags'] = $value;
        }
    }

    public function comments()
    {
        return $this->hasMany(ResourceComment::class);
    }

    /**
     * Check if this resource has student comments that the teacher hasn't replied to
     */
    public function hasUnrepliedStudentComments($teacherId = null)
    {
        if (!$teacherId) {
            $teacherId = $this->teacher_id;
        }

        if (!$teacherId) {
            return false;
        }

        // Get all student comments (not replies) on this resource
        $studentComments = $this->comments()
            ->whereHas('user', function($query) {
                $query->where('account_type', 'student');
            })
            ->whereNull('parent_id')
            ->get();

        // Check if teacher has replied to any of these student comments
        foreach ($studentComments as $comment) {
            $teacherReplied = $this->comments()
                ->where('parent_id', $comment->id)
                ->whereHas('user', function($query) use ($teacherId) {
                    $query->where('id', $teacherId);
                })
                ->exists();

            if (!$teacherReplied) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get count of student comments that teacher hasn't replied to
     */
    public function getUnrepliedStudentCommentsCount($teacherId = null)
    {
        if (!$teacherId) {
            $teacherId = $this->teacher_id;
        }

        if (!$teacherId) {
            return 0;
        }

        $count = 0;
        $studentComments = $this->comments()
            ->whereHas('user', function($query) {
                $query->where('account_type', 'student');
            })
            ->whereNull('parent_id')
            ->get();

        foreach ($studentComments as $comment) {
            $teacherReplied = $this->comments()
                ->where('parent_id', $comment->id)
                ->whereHas('user', function($query) use ($teacherId) {
                    $query->where('id', $teacherId);
                })
                ->exists();

            if (!$teacherReplied) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get count of student comments that teacher has replied to
     */
    public function getRepliedStudentCommentsCount($teacherId = null)
    {
        if (!$teacherId) {
            $teacherId = $this->teacher_id;
        }

        if (!$teacherId) {
            return 0;
        }

        $count = 0;
        $studentComments = $this->comments()
            ->whereHas('user', function($query) {
                $query->where('account_type', 'student');
            })
            ->whereNull('parent_id')
            ->get();

        foreach ($studentComments as $comment) {
            $teacherReplied = $this->comments()
                ->where('parent_id', $comment->id)
                ->whereHas('user', function($query) use ($teacherId) {
                    $query->where('id', $teacherId);
                })
                ->exists();

            if ($teacherReplied) {
                $count++;
            }
        }

        return $count;
    }
}
