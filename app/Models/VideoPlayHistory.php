<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoPlayHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'resource_id',
        'played_at',
        'watch_duration',
        'completed'
    ];

    protected $casts = [
        'played_at' => 'datetime',
        'completed' => 'boolean',
        'watch_duration' => 'integer'
    ];

    /**
     * Get the user that played the video.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the resource (video) that was played.
     */
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }
}
