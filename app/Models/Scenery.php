<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scenery extends Model
{
    use HasFactory;

    protected $casts = [
        'years' => 'array'
    ];

    protected $appends = ['dynamic_years'];


    public function getDynamicYearsAttribute()
    {
        return [];
    }
}
