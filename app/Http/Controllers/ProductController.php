<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Listar productos",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="all",
     *         in="query",
     *         required=false,
     *         description="Si está presente, retorna todos los productos sin paginación",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de productos (paginada o completa)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron productos"
     *     )
     * )
     */
    public function index(Request $request)
    {

        if ($request->has('all')) {
            $products = Product::all();
        } else {
            $products = Product::paginate(10);
        }
        if ($products) {
            return response()->json($products, 200);
        }
        return response()->json([
            'message' => 'No se encontraron productos',
        ], 404);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Crear un nuevo producto",
     *     tags={"Productos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 ref="#/components/schemas/StoreProductRequest"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Producto creado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('products', 'public');
        }
        $product = Product::create($data);
        return response()->json($product, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Obtener un producto específico",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del producto"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado"
     *     )
     * )
     */
    public function show(Product $product)
    {
        return response()->json($product, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/products/{id}",
     *     summary="Actualizar un producto existente",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 ref="#/components/schemas/UpdateProductRequest"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto actualizado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado"
     *     )
     * )
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        // Si nos manda una imagen
        if ($request->hasFile('image_path')) {
            // 1. Borrar la anterior
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }
            // 2. Guardar la nueva
            $data['image_path'] = $request->file('image_path')->store('products', 'public');
        } else {
            // Si no nos manda una imagen quitamos el campo
            unset($data['image_path']);
        }
        $product->update($data);
        return response()->json($product, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Eliminar un producto",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto eliminado correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado"
     *     )
     * )
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
