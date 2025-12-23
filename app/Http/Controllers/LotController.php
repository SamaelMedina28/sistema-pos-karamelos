<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lot;

class LotController extends Controller
{
    // ? Ver todos los lotes
    public function index(Request $request)
    {
        $lots = Lot::when($request->date, function ($query) use ($request) {
            $query->where('created_at', 'like', "%{$request->date}%");
        })->with('cuts', 'sales')->orderBy('id', 'desc')->paginate(10);
        return response()->json($lots, 200);
    }

    // ? Ver un lote
    public function show(Lot $lot)
    {
        return response()->json($lot->load('cuts', 'sales.products'), 200);
    }
}
