<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
