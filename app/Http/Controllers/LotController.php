<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lot;

class LotController extends Controller
{
    // ? Ver todos los lotes
    public function index()
    {
        $lots = Lot::with('cuts', 'sales.products')->get();
        return response()->json($lots, 200);
    }

    // ? Ver un lote
    public function show(Lot $lot)
    {
        return response()->json($lot->load('cuts', 'sales.products'), 200);
    }
}
