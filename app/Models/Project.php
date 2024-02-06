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

    public function nodes()
    {
        return $this->hasMany('App\Models\Node');
    }
}
