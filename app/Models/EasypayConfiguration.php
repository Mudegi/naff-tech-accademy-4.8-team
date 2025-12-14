<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EasypayConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'secret',
        'website_url',
        'ipn_url',
        'hits',
        'is_active'
    ];

    protected $casts = [
        'hits' => 'integer',
        'is_active' => 'boolean'
    ];
} 