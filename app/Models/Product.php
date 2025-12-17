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
}
