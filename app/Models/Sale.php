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

    public function products()
    {
        return $this->belongsToMany(Product::class, 'sale_details', 'sale_id', 'product_id')
            ->withPivot('grams', 'amount');
    }
}
