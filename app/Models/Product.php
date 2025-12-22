<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Asignacion masiva
    protected $fillable = [
        'name',
        'image_path',
        'price_for_kg',
        'stock_quantity',
    ];

    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'sale_details', 'product_id', 'sale_id')
            ->withPivot('grams', 'amount');
    }
}
