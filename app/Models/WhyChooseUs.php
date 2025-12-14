<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhyChooseUs extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
    ];

    public function features()
    {
        return $this->hasMany(WhyChooseUsFeature::class)->orderBy('order', 'asc');
    }

    public function activeFeatures()
    {
        return $this->hasMany(WhyChooseUsFeature::class)
            ->where('is_active', true)
            ->orderBy('order', 'asc');
    }
}
