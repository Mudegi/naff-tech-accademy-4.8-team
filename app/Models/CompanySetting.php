<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name',
        'company_email',
        'company_phone',
        'company_address',
        'company_logo',
        'company_website',
        'company_description',
        'tax_number',
        'currency',
        'timezone',
        'bank_name',
        'account_name',
        'account_number',
        'mtn_mobile_number',
        'mtn_registered_name',
        'airtel_mobile_number',
        'airtel_registered_name',
    ];

    protected $casts = [
        'company_logo' => 'string',
        'bank_name' => 'string',
        'account_name' => 'string',
        'account_number' => 'string',
        'mtn_registered_name' => 'string',
        'mtn_mobile_number' => 'string',
        'airtel_registered_name' => 'string',
        'airtel_mobile_number' => 'string',
    ];
}
