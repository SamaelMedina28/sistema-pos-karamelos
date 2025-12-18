<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cut extends Model
{
    protected $fillable = [
        'type',
        'clerk',
        'cash_system',
        'card_system',
        'total_system',
        'cash_counted',
        'card_counted',
        'total_counted',
        'difference',
    ];
}
