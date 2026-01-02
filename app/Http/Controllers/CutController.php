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
    /**
     * @OA\Get(
     *     path="/api/cuts/{lot_id}",
     *     summary="Ver todos los cortes de un lote",
     *     tags={"Cortes"},
     *     @OA\Parameter(
     *         name="lot_id",
     *         in="path",
     *         required=true,
     *         description="ID del lote",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de cortes"
     *     )
     * )
     */
    public function index($lot_id)
    {
        return response()->json(Cut::where('lot_id', $lot_id)->get(), 200);
    }
    /**
     * @OA\Post(
     *     path="/api/cuts",
     *     summary="Crear un corte (X o Z)",
     *     tags={"Cortes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type","cash_counted","card_counted"},
     *             @OA\Property(property="type", type="string", enum={"x", "z"}, example="z"),
     *             @OA\Property(property="cash_counted", type="number", example=100.50),
     *             @OA\Property(property="card_counted", type="number", example=200.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Corte creado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación"
     *     )
     * )
     */
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
        if ($request->type == 'z') {
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
