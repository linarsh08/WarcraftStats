<?php

namespace App\Models;

class Stat extends Model
{
    protected $fillable = ['played_games', 'wins', 'most_selectable_race'];
    protected $hidden = ['user_id'];
}
