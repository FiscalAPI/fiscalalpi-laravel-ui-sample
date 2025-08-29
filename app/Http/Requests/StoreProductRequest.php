<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'description' => 'required|string|max:500',
            'unitPrice' => 'required|numeric|min:0',
            'fiscalapiId' => 'nullable|string|max:255',
            'sat_unit_measurement_id' => 'required|string|exists:sat_unit_measurement_codes,code',
            'sat_tax_object_id' => 'required|string|exists:sat_tax_object_codes,code',
            'sat_product_code_id' => 'required|string|exists:sat_product_codes,code',
        ];
    }
}
