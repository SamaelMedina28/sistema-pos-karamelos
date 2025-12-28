<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'image_path' => 'nullable' . ($this->hasFile('image_path') ? '|image|mimes:jpeg,png,jpg,gif,webp|max:2048' : ''),
            'price_for_kg' => 'required|numeric',
            'stock_quantity' => 'required|numeric',
        ];
    }
}
