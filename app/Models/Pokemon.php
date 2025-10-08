<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    protected $fillable = [
        'id',
        'name',
        'height',
        'category',
        'weight',
        'types',
        'ps',
        'attack',
        'defense',
        'attack_special',
        'defense_special',
        'speed',
        'color',
        'evolve_to',
        'evolve_from',
        'evolution_stage'
    ];
}
