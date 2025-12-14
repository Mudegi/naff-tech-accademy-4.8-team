<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class University extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'url_pattern',
        'base_url',
        'scraper_type',
        'cut_off_format',
        'scraper_config',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'scraper_config' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the cut-offs for this university.
     */
    public function cutOffs()
    {
        return $this->hasMany(UniversityCutOff::class, 'university_name', 'name');
    }

    /**
     * Scope to get active universities only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the URL for a specific academic year.
     */
    public function getUrlForYear($academicYear = null)
    {
        $academicYear = $academicYear ?? date('Y');
        $nextYear = $academicYear + 1;

        if ($this->url_pattern) {
            return str_replace(
                ['{year}', '{nextYear}'],
                [$academicYear, $nextYear],
                $this->url_pattern
            );
        }

        return $this->base_url;
    }

    /**
     * Check if this university has a configured URL pattern.
     */
    public function hasUrlPattern()
    {
        return !empty($this->url_pattern);
    }
}
