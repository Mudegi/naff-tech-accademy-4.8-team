<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlutterwaveSetting extends Model
{
    use HasFactory;

    protected $table = 'flutterwave_settings';

    protected $fillable = [
        'public_key',
        'secret_key',
        'encryption_key',
        'test_mode',
        'webhook_secret',
        'currency_code',
        'created_by',
        'updated_by',
    ];
} 