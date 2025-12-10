<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class P2pCryptoTradingPairResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'base_currency_id' => $this->base_currency_id,
            'quote_currency_id' => $this->quote_currency_id,
            'pair_name' => $this->pair_name,
            'symbol' => $this->symbol,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'min_quantity' => $this->min_quantity,
            'max_quantity' => $this->max_quantity,
            'price_tick_size' => $this->price_tick_size,
            'quantity_step_size' => $this->quantity_step_size,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            // Relationships
            'base_currency' => new CryptoCurrencyResource($this->whenLoaded('baseCurrency')),
            'quote_currency' => new CryptoCurrencyResource($this->whenLoaded('quoteCurrency')),
        ];
    }
}
