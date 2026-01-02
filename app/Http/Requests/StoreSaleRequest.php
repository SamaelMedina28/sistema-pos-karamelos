<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreSaleRequest",
 *     type="object",
 *     title="Petición para crear venta",
 *     required={"clerk", "client", "payment_method", "sale_details"},
 *     @OA\Property(property="clerk", type="string", description="Nombre del vendedor"),
 *     @OA\Property(property="client", type="string", description="Nombre del cliente"),
 *     @OA\Property(property="payment_method", type="string", enum={"cash","card","mix"}, description="Método de pago"),
 *     @OA\Property(property="cash", type="number", format="float", description="Monto en efectivo"),
 *     @OA\Property(property="card", type="number", format="float", description="Monto en tarjeta"),
 *     @OA\Property(
 *         property="sale_details",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"product_id", "grams"},
 *             @OA\Property(property="product_id", type="integer", description="ID del producto"),
 *             @OA\Property(property="grams", type="number", format="float", description="Gramos vendidos")
 *         )
 *     )
 * )
 */
class StoreSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'clerk' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,card,mix',
            'cash' => 'required_if:payment_method,cash,mix|numeric',
            'card' => 'required_if:payment_method,card,mix|numeric',
            'sale_details' => 'required|array',
            'sale_details.*.product_id' => 'required|exists:products,id',
            'sale_details.*.grams' => 'required|numeric|min:0.1',
        ];
    }
}
