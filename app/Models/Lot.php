<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    protected $fillable = [
        'total',
        'total_cash',
        'total_card',
        'difference',
    ];
}
