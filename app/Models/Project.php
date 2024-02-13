<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $casts = [
        'sceneries' => 'array'
    ];

    protected $appends = [
        'years'
    ];

    public function nodes()
    {
        return $this->hasMany('App\Models\Node');
    }

    public function getYearsAttribute()
    {
        $years = [];
        while ($this->year_from <= $this->year_to) {
            $years[] = $this->year_from;
            $this->year_from++;
        }

        return $years;
    }
}
