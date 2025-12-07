<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price, // This is our accessor for formatted price
            'currency' => [
                'code' => $this->currency_code,
                'name' => $this->currency ? $this->currency->name : null,
                'symbol' => $this->currency ? $this->currency->symbol : null,
            ],
            'condition' => $this->condition,
            'status' => $this->status,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'contact_phone' => $this->contact_phone,
            'negotiable' => $this->negotiable,
            'view_count' => $this->view_count,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'images' => AdImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
