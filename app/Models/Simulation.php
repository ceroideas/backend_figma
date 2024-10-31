<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
    use HasFactory;

    protected $casts = [
        'nodes' => 'array',
        'samples' => 'array',
        'operation_data' => 'array'
    ];

    public function getSimulationAttribute($value)
    {
        return url('/simulations',$value);
    }
}
