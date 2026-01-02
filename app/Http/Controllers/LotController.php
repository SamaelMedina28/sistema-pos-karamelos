<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lot;

class LotController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/lots",
     *     summary="Listar lotes",
     *     tags={"Lotes"},
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=false,
     *         description="Filtrar por fecha (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de lotes paginada"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $lots = Lot::when($request->date, function ($query) use ($request) {
            $query->where('created_at', 'like', "%{$request->date}%");
        })->with('cuts', 'sales')->orderBy('id', 'desc')->paginate(10);
        return response()->json($lots, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/lots/{id}",
     *     summary="Ver detalles de un lote",
     *     tags={"Lotes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del lote",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del lote con cortes y ventas"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lote no encontrado"
     *     )
     * )
     */
    public function show(Lot $lot)
    {
        return response()->json($lot->load('cuts', 'sales.products'), 200);
    }
}
