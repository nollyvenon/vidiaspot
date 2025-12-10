<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreP2pCryptoTradingOrderRequest extends FormRequest
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
            'trading_pair_id' => 'required|exists:p2p_crypto_trading_pairs,id',
            'order_type' => 'required|in:market,limit,stop_loss,stop_limit,market_maker,trailing_stop',
            'side' => 'required|in:buy,sell',
            'quantity' => 'required|numeric|min:0.00000001',
            'price' => 'nullable|numeric|min:0.00000001', // Required for limit orders
            'stop_price' => 'nullable|numeric|min:0.00000001', // Required for stop orders
            'time_in_force' => 'nullable|in:GTC,IOC,FOK,GTD',
            'good_till_date' => 'nullable|date_format:Y-m-d H:i:s',
            'post_only' => 'nullable|boolean',
            'reduce_only' => 'nullable|boolean',
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
            'trading_pair_id.required' => 'The trading pair is required.',
            'trading_pair_id.exists' => 'The selected trading pair is invalid.',
            'order_type.required' => 'The order type is required.',
            'order_type.in' => 'Invalid order type. Valid types are: market, limit, stop_loss, stop_limit, market_maker, trailing_stop.',
            'side.required' => 'The order side (buy or sell) is required.',
            'side.in' => 'The order side must be either buy or sell.',
            'quantity.required' => 'The quantity is required.',
            'quantity.numeric' => 'The quantity must be a valid number.',
            'quantity.min' => 'The quantity must be greater than 0.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price must be greater than 0.',
            'stop_price.numeric' => 'The stop price must be a valid number.',
            'stop_price.min' => 'The stop price must be greater than 0.',
            'good_till_date.date_format' => 'The good till date format is invalid. Use Y-m-d H:i:s format.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure boolean values are properly cast
        if ($this->has('post_only')) {
            $this->merge([
                'post_only' => $this->post_only ? true : false,
            ]);
        }

        if ($this->has('reduce_only')) {
            $this->merge([
                'reduce_only' => $this->reduce_only ? true : false,
            ]);
        }
    }
}
