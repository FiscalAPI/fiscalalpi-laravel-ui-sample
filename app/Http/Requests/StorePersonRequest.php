<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonRequest extends FormRequest
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
            'fiscalapiId' => 'nullable|string|max:255',
            'legalName' => 'required|string|max:500',
            'email' => 'required|email|unique:people,email|max:255',
            'password' => 'required|string|min:8|max:255',
            'capitalRegime' => 'nullable|string|max:255',
            'satTaxRegimeId' => 'nullable|string|exists:sat_tax_regime_codes,code',
            'satCfdiUseId' => 'nullable|string|exists:sat_cfdi_use_codes,code',
            'tin' => 'nullable|string|max:13',
            'zipCode' => 'nullable|string|max:5',
            'taxPassword' => 'nullable|string|min:8|max:255',
        ];
    }
}
