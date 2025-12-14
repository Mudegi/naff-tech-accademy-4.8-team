<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Vinkla\Hashids\Facades\Hashids;
use App\Models\Concerns\TenantScope;

class Topic extends Model
{
    use HasFactory, TenantScope;

    protected $fillable = [
        'subject_id',
        'name',
        'slug',
        'description',
        'order',
        'is_active',
        'school_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function getHashIdAttribute()
    {
        return Hashids::encode($this->id);
    }

    public static function findByHashId($hash_id)
    {
        $decoded = Hashids::decode($hash_id);
        if (count($decoded) === 0) {
            return null;
        }
        return self::find($decoded[0]);
    }
}
