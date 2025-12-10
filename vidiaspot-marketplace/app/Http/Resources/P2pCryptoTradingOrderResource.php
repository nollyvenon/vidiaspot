<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class P2pCryptoTradingOrderResource extends JsonResource
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
            'user_id' => $this->user_id,
            'trading_pair_id' => $this->trading_pair_id,
            'order_type' => $this->order_type,
            'side' => $this->side,
            'quantity' => $this->quantity,
            'executed_quantity' => $this->executed_quantity,
            'price' => $this->price,
            'stop_price' => $this->stop_price,
            'avg_price' => $this->avg_price,
            'status' => $this->status,
            'time_in_force' => $this->time_in_force,
            'good_till_date' => $this->good_till_date?->toISOString(),
            'post_only' => $this->post_only,
            'reduce_only' => $this->reduce_only,
            'fee' => $this->fee,
            'fee_currency' => $this->fee_currency,
            'notes' => $this->notes,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            // Relationships
            'user' => new UserResource($this->whenLoaded('user')),
            'trading_pair' => new P2pCryptoTradingPairResource($this->whenLoaded('tradingPair')),
            'trade_executions' => P2pCryptoTradeExecutionResource::collection($this->whenLoaded('tradeExecutions')),
        ];
    }
}
