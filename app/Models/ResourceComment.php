<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'resource_id',
        'user_id',
        'comment',
        'parent_id',
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(ResourceCommentLike::class, 'resource_comment_id')->where('type', 'like');
    }
    public function dislikes()
    {
        return $this->hasMany(ResourceCommentLike::class, 'resource_comment_id')->where('type', 'dislike');
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getDislikesCountAttribute()
    {
        return $this->dislikes()->count();
    }

    // For replies (if parent_id is enabled in the future)
    // public function parent()
    // {
    //     return $this->belongsTo(ResourceComment::class, 'parent_id');
    // }
    public function replies()
    {
        return $this->hasMany(ResourceComment::class, 'parent_id')->with(['user', 'likes', 'dislikes', 'replies']);
    }
} 