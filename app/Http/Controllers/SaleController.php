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
    public function __construct(private SaleService $saleService)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::with('sale_details.product')->orderBy('id', 'desc')->paginate();

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
    public function store(StoreSaleRequest $request)
    {
        try {
            $sale = DB::transaction(function () use ($request) {
                // Sacamos el id de todos los productos que nos envian
                $ids = array_column($request->sale_details, 'product_id');
                // Buscamos todos los productos, ponemos keyBy para que sea mas rapido buscarlos
                $products = Product::whereIn('id', $ids)->get()->keyBy('id');
                // Sumamos el precio de todos los productos
                // $total = $products->sum('price_for_kg') * $request->sale_details->sum('grams') / 1000;
                $total = 0;
                foreach ($request->sale_details as $saleDetail) {
                    $product = $products->get($saleDetail['product_id']);
                    $total += $product->price_for_kg * $saleDetail['grams'] / 1000;
                }
                // Redondeamos el total
                $total = round($total, 2);
                // Verificamos que haya pagado lo suficiente
                if (($request->cash ?? 0) + ($request->card ?? 0) < $total) {
                    throw new \Exception("No se ha pagado lo suficiente: Total: $total, Pagado: $request->cash + $request->card");
                }
                // Checamos con que lote lo asignamos (el ultimo que haya, si no hay creamos uno)
                $lastLot = Lot::latest('id')->first() ?: Lot::create([
                    'total' => 0,
                    'total_cash' => 0,
                    'total_card' => 0,
                    'difference' => 0
                ]);
                // ~ Ejemplo
                // total = 100
                // card = 80
                // cash = 60
                // mixto
                // cash = 100 - 80 = 20 o si no hay card, cash = 100 - 0 = 100 osea que pago todo en efectivo asi que se considerara solo eso para el cambio
                // ? Estamos calculando cuando es el cash real que ocupa la venta, no lo que nos mandaron
                $cash = max(0, $total - ($request->card ?? 0));
                // card = 80 o si no hay, card = 0
                $card = $request->card ?? 0;
                // change = 60 - (100 - 80) = 60 - 20 = 40 de cambio
                $change = ($request->cash ?? 0) - ($total - ($request->card ?? 0));

                // Creamos la venta
                $sale = Sale::create([
                    'clerk' => $request->clerk,
                    'client' => $request->client,
                    'payment_method' => $request->payment_method,
                    'cash' => $cash,
                    'card' => $card,
                    'change' => $change,
                    'total' => $total,
                    'lot_id' => $lastLot->id,
                ]);
                // Creamos los detalles de la venta con cada uno de los productos
                // Saledetails contiene el array que nos mandan
                foreach ($request->sale_details as $saleDetail) {
                    $product = $products->get($saleDetail['product_id']);
                    $sale->sale_details()->create([
                        'product_id' => $saleDetail['product_id'],
                        'grams' => $saleDetail['grams'],
                        'amount' => round($saleDetail['grams'] * $product->price_for_kg / 1000, 2),
                    ]);
                    $product->decrement('stock_quantity', $saleDetail['grams']);
                }
                // Actualizamos el lote
                $lastLot->update([
                    'total' => $lastLot->total + $total,
                    'total_cash' => $lastLot->total_cash + $cash,
                    'total_card' => $lastLot->total_card + $card,
                ]);
                return $sale;
            });
            return response()->json($sale->load('sale_details.product'), 201);
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
