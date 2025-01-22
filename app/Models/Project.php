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
        'years','clean_sceneries'
    ];

    public function nodes()
    {
        return $this->hasMany('App\Models\Node')->orderBy('id','desc');
    }

    public function getYearsAttribute()
    {
        $years = [];
        $start = $this->year_from;
        while ($start <= $this->year_to) {
            $years[] = $start;
            $start++;
        }

        return $years;
    }

    public function getCleanSceneriesAttribute()
    {
        $sceneries = [];
        foreach ($this->sceneries as $key => $sc) {
            $years = [];
            $start = $this->year_from;
            
            while ($start <= $this->year_to) {
                $years[$start] = 0;
                $start++;
            }
            $sceneries[] = ["name"=>$sc, "years"=>$years];
        }

        return $sceneries;

    }

    public function getThumbAttribute($value)
    {
        return url('/projects',$value);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
