<?php

namespace App\Models;

class OrderStep extends Model
{
    protected $fillable = ['description', 'timing'];
    protected $hidden = ['build_order_id'];

    protected $rules = [
        'description' => 'required|string|min:20',
        'timing' => 'required|date_format:HH:MM:ll',
    ];
}
