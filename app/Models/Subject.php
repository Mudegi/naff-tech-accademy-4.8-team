<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;
use App\Models\Concerns\TenantScope;

class Subject extends Model
{
    use HasFactory, TenantScope;

    protected $fillable = [
        'name',
        'subject_id',
        'slug',
        'description',
        'content',
        'duration',
        'total_topics',
        'total_resources',
        'passing_score',
        'image',
        'is_active',
        'objectives',
        'learning_outcomes',
        'prerequisites',
        'assessment_methods',
        'school_id',
        'level',
        'paper_count',
        'papers',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_topics' => 'integer',
        'total_resources' => 'integer',
        'passing_score' => 'float',
        'objectives' => 'array',
        'learning_outcomes' => 'array',
        'prerequisites' => 'array',
        'assessment_methods' => 'array',
        'paper_count' => 'integer',
        'papers' => 'array',
        'level' => 'string',
    ];

    /**
     * Get the objectives as an array.
     *
     * @return array
     */
    public function getObjectivesArrayAttribute()
    {
        if (is_string($this->objectives)) {
            return json_decode($this->objectives, true) ?? [];
        }
        return $this->objectives ?? [];
    }

    /**
     * Get the learning outcomes as an array.
     *
     * @return array
     */
    public function getLearningOutcomesArrayAttribute()
    {
        if (is_string($this->learning_outcomes)) {
            return json_decode($this->learning_outcomes, true) ?? [];
        }
        return $this->learning_outcomes ?? [];
    }

    /**
     * Get the prerequisites as an array.
     *
     * @return array
     */
    public function getPrerequisitesArrayAttribute()
    {
        if (is_string($this->prerequisites)) {
            return json_decode($this->prerequisites, true) ?? [];
        }
        return $this->prerequisites ?? [];
    }

    /**
     * Get the assessment methods as an array.
     *
     * @return array
     */
    public function getAssessmentMethodsArrayAttribute()
    {
        if (is_string($this->assessment_methods)) {
            return json_decode($this->assessment_methods, true) ?? [];
        }
        return $this->assessment_methods ?? [];
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
//    public function getRouteKeyName()
//    {
//        return 'hash_id';
//    }

    /**
     * Get the hashed ID for the subject.
     *
     * @return string
     */
    public function getHashIdAttribute()
    {
        return Hashids::encode($this->id);
    }

    /**
     * Find a subject by its hashed ID.
     *
     * @param string $hash_id
     * @return Subject|null
     */
    public static function findByHashId($hash_id)
    {
        $decoded = Hashids::decode($hash_id);
        if (count($decoded) === 0) {
            return null;
        }
        return self::find($decoded[0]);
    }
}
