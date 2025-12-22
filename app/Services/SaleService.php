<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Lot;
use App\Models\Sale;

class SaleService
{
    public function createSale($request)
    {
        return DB::transaction(function () use ($request) {
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

            $paymentData = $this->isValidPayment($total, $request->cash, $request->card, $request->payment_method);
            
            if (!$paymentData['valid']) {
                throw new \Exception("No se ha pagado lo suficiente: Total: $total, Pagado: Cash: " . $paymentData['cash'] . " Card: " . $paymentData['card']);
            }
            // Checamos con que lote lo asignamos (el ultimo que haya, si no hay creamos uno)
            $lastLot = self::getLastLot();
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
                $sale->products()->attach($saleDetail['product_id'], [
                    'grams' => $saleDetail['grams'],
                    'amount' => round($saleDetail['grams'] * $product->price_for_kg / 1000, 2),
                ]);
                // ! Verificar que el stock sea suficiente
                if ($product->stock_quantity < $saleDetail['grams'] / 1000) {
                    throw new \Exception("No hay stock suficiente para el producto: " . $product->name);
                }
                // Stock esta en kilos
                $product->decrement('stock_quantity', $saleDetail['grams'] / 1000);
            }
            // Actualizamos el lote
            $lastLot->update([
                'total' => $lastLot->total + $total,
                'total_cash' => $lastLot->total_cash + $cash,
                'total_card' => $lastLot->total_card + $card,
            ]);
            return $sale;
        });
    }

    // Verificamos que haya pagado lo suficiente segun el metodo de pago
    private function isValidPayment($total, $cash, $card, $method): array
    {
        switch ($method) {
            case 'cash':
                return [
                    'cash' => $cash,
                    'card' => 0,
                    'valid' => $total <= $cash,
                ];
            case 'card':
                return [
                    'cash' => 0,
                    'card' => $card,
                    'valid' => $total <= $card,
                ];
            default:
                return [
                    'cash' => $cash,
                    'card' => $card,
                    'valid' => $total <= $cash + $card,
                ];
        }
    }


    public static function getLastLot(){
        return Lot::latest('id')->first() ?: Lot::create([
            'total' => 0,
            'total_cash' => 0,
            'total_card' => 0,
            'difference' => 0
        ]);
    }
}