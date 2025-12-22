<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cut;
use App\Models\Lot;
use App\Services\SaleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CutController extends Controller
{
    // ? Ver todos los cortes de un dia
    public function index($date)
    {
        return response()->json(Cut::where('created_at', 'LIKE', $date)->get(), 200);
    }
    // ? Crear un corte
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'cash_counted' => 'required',
            'card_counted' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $lastLot = Lot::latest('id')->first() ?: Lot::create([
            'total' => 0,
            'total_cash' => 0,
            'total_card' => 0,
            'difference' => 0
        ]);
        $totalCounted = $request->cash_counted + $request->card_counted;
        if($request->type == 'z'){
            $lastLot->update([
                'difference' => $totalCounted - $lastLot->total
            ]);
            // Crear un nuevo lote para futuros cortes y ventas
            Lot::create([
                'total' => 0,
                'total_cash' => 0,
                'total_card' => 0,
                'difference' => 0
            ]);
        }
        $cut = Cut::create([
            'type' => $request->type,
            'clerk' => Auth::user()->name,
            'cash_system' => $lastLot->total_cash,
            'card_system' => $lastLot->total_card,
            'total_system' => $lastLot->total,
            'cash_counted' => $request->cash_counted,
            'card_counted' => $request->card_counted,
            'total_counted' => $totalCounted,
            'difference' => $totalCounted - $lastLot->total,
            'lot_id' => $lastLot->id,
        ]);
        return response()->json($cut, 201);
    }
}
