<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class P2pCryptoTradeExecutionResource extends JsonResource
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
            'trading_order_id' => $this->trading_order_id,
            'maker_order_id' => $this->maker_order_id,
            'taker_order_id' => $this->taker_order_id,
            'trading_pair_id' => $this->trading_pair_id,
            'side' => $this->side,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'fee' => $this->fee,
            'fee_currency' => $this->fee_currency,
            'fee_payer' => $this->fee_payer,
            'executed_at' => $this->executed_at->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            // Relationships
            'trading_pair' => new P2pCryptoTradingPairResource($this->whenLoaded('tradingPair')),
        ];
    }
}
