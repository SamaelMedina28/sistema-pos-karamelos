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
     * Display a listing of the resource.
     */
    public function index($lot_id)
    {
        $sales = Sale::with('sale_details.product')->where('lot_id', $lot_id)->orderBy('id', 'desc')->paginate();

        return response()->json($sales, 200);
    }

    /**
     * Formato JSON que espera la API
     * {
     *  "clerk": "Juan",
     *  "client": "Pedro",
     *  "payment_method": "cash",
     *  "cash": 10,
     *  "card": 0,
     *  "sale_details": [
     *      {
     *          "product_id": 1,
     *          "grams": 12,
     *      },
     *      {
     *          "product_id": 2,
     *          "grams": 32,
     *      }
     *  ]
     * }
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
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale = $sale->load('sale_details.product');
        return response()->json($sale, 200);
    }
}
