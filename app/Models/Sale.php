<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'clerk',
        'client',
        'total',
        'cash',
        'card',
        'change',
        'lot_id',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }
}
