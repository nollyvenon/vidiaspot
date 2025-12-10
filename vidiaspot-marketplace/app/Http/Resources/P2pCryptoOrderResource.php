<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class P2pCryptoOrderResource extends JsonResource
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
            'seller_id' => $this->seller_id,
            'buyer_id' => $this->buyer_id,
            'crypto_currency_id' => $this->crypto_currency_id,
            'order_type' => $this->order_type,
            'amount' => $this->amount,
            'price_per_unit' => $this->price_per_unit,
            'total_amount' => $this->total_amount,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'matched_at' => $this->matched_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'terms_and_conditions' => $this->terms_and_conditions,
            'additional_notes' => $this->additional_notes,
            'crypto_transaction_id' => $this->crypto_transaction_id,
            'payment_transaction_id' => $this->payment_transaction_id,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            // Relationships
            'crypto_currency' => new CryptoCurrencyResource($this->whenLoaded('cryptoCurrency')),
            'seller' => new UserResource($this->whenLoaded('seller')),
            'buyer' => new UserResource($this->whenLoaded('buyer')),
            'escrow' => new P2pCryptoEscrowResource($this->whenLoaded('escrow')),
        ];
    }
}
