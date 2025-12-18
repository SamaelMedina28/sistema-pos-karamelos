<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Traer todos los productos paginados
        $products = Product::paginate(10);
        if ($products) {
            return response()->json($products, 200);
        }
        return response()->json([
            'message' => 'No se encontraron productos',
        ], 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('products');
        }
        $product = Product::create($data);
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Traer un producto por id
        if ($product) {
            return response()->json($product, 200);
        }
        return response()->json([
            'message' => 'No se encontro el producto',
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //eliminar la imagen del producto
        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();
        return response()->json([
            'message' => 'Producto eliminado correctamente',
        ], 200);
    }
}
