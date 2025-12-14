<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceCommentLike extends Model
{
    protected $fillable = [
        'user_id',
        'resource_comment_id',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comment()
    {
        return $this->belongsTo(ResourceComment::class, 'resource_comment_id');
    }
} 