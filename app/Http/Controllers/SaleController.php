<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Lot;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\SaleService;

class SaleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/sales",
     *     summary="Listar ventas",
     *     tags={"Ventas"},
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=false,
     *         description="Filtrar por fecha (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de ventas paginada"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $sales = Sale::when($request->has('date'), function ($query) use ($request) {
            return $query->where('created_at', 'like', "%{$request->date}%");
        })->orderBy('id', 'desc')->paginate(10);

        return response()->json($sales, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/sales",
     *     summary="Registrar una nueva venta",
     *     tags={"Ventas"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos de la venta y productos",
     *         @OA\JsonContent(
     *             required={"clerk","client","payment_method","cash","card","sale_details"},
     *             @OA\Property(property="clerk", type="string", example="Juan"),
     *             @OA\Property(property="client", type="string", example="Pedro"),
     *             @OA\Property(property="payment_method", type="string", enum={"cash","card","mixed"}, example="cash"),
     *             @OA\Property(property="cash", type="number", example=10.00),
     *             @OA\Property(property="card", type="number", example=0.00),
     *             @OA\Property(
     *                 property="sale_details",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"product_id","grams"},
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="grams", type="number", example=120)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Venta creada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor"
     *     )
     * )
     */
    public function store(StoreSaleRequest $request, SaleService $saleService)
    {
        try {
            $sale = $saleService->createSale($request);
            return response()->json($sale->load('products'), 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'status' => 500,
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/sales/{id}",
     *     summary="Ver detalles de una venta",
     *     tags={"Ventas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la venta",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la venta con productos"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Venta no encontrada"
     *     )
     * )
     */
    public function show(Sale $sale)
    {
        $sale = $sale->load('products');
        return response()->json($sale, 200);
    }
}
