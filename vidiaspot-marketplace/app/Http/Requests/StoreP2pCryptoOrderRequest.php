<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreP2pCryptoOrderRequest extends FormRequest
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
            'crypto_currency_id' => 'required|exists:crypto_currencies,id',
            'order_type' => 'required|in:buy,sell',
            'amount' => 'required|numeric|min:0.00000001',
            'price_per_unit' => 'required|numeric|min:0.00000001',
            'payment_method' => 'required|string|max:255',
            'terms_and_conditions' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'payment_method_id' => 'nullable|exists:p2p_crypto_payment_methods,id', // If using stored payment method
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'crypto_currency_id.required' => 'The cryptocurrency is required.',
            'crypto_currency_id.exists' => 'The selected cryptocurrency is invalid.',
            'order_type.required' => 'The order type (buy or sell) is required.',
            'order_type.in' => 'The order type must be either buy or sell.',
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must be a valid number.',
            'amount.min' => 'The amount must be greater than 0.',
            'price_per_unit.required' => 'The price per unit is required.',
            'price_per_unit.numeric' => 'The price per unit must be a valid number.',
            'price_per_unit.min' => 'The price per unit must be greater than 0.',
            'payment_method.required' => 'The payment method is required.',
            'payment_method.max' => 'The payment method name is too long.',
        ];
    }
}
