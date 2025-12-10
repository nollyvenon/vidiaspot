<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class P2pCryptoEscrowResource extends JsonResource
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
            'p2p_order_id' => $this->p2p_order_id,
            'crypto_transaction_id' => $this->crypto_transaction_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'released_at' => $this->released_at?->toISOString(),
            'refunded_at' => $this->refunded_at?->toISOString(),
            'release_notes' => $this->release_notes,
            'refund_notes' => $this->refund_notes,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
