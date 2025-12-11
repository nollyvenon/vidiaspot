<?php

namespace App\Http\Resources\FarmProducts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmProductResource extends JsonResource
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
            'currency_code' => $this->currency_code,
            'formatted_price' => $this->formatted_price,
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
            
            // Farm-specific attributes
            'direct_from_farm' => $this->direct_from_farm,
            'farm_name' => $this->farm_name,
            'is_organic' => $this->is_organic,
            'harvest_date' => $this->harvest_date,
            'farm_location' => $this->farm_location,
            'farm_latitude' => $this->farm_latitude,
            'farm_longitude' => $this->farm_longitude,
            'certification' => $this->certification,
            'harvest_season' => $this->harvest_season,
            'farm_size' => $this->farm_size,
            
            // Extended farm-specific attributes
            'freshness_days' => $this->freshness_days,
            'quality_rating' => $this->quality_rating,
            'seasonal_availability' => $this->seasonal_availability,
            'certification_type' => $this->certification_type,
            'certification_body' => $this->certification_body,
            'farm_practices' => $this->farm_practices,
            'delivery_options' => $this->delivery_options,
            'minimum_order' => $this->minimum_order,
            'packaging_type' => $this->packaging_type,
            'shelf_life' => $this->shelf_life,
            'storage_instructions' => $this->storage_instructions,
            'farm_certifications' => $this->farm_certifications,
            'pesticide_use' => $this->pesticide_use,
            'irrigation_method' => $this->irrigation_method,
            'soil_type' => $this->soil_type,
            'sustainability_score' => $this->sustainability_score,
            'carbon_footprint' => $this->carbon_footprint,
            'farm_tour_available' => $this->farm_tour_available,
            'farm_story' => $this->farm_story,
            'farmer_name' => $this->farmer_name,
            'farmer_image' => $this->farmer_image,
            'farmer_bio' => $this->farmer_bio,
            'harvest_method' => $this->harvest_method,
            'post_harvest_handling' => $this->post_harvest_handling,
            'supply_capacity' => $this->supply_capacity,
            'shipping_availability' => $this->shipping_availability,
            'local_delivery_radius' => $this->local_delivery_radius,
            'distance' => $this->distance ?? null, // For proximity searches
            
            // Relationships
            'user' => new \App\Http\Resources\UserResource($this->whenLoaded('user')),
            'category' => new \App\Http\Resources\CategoryResource($this->whenLoaded('category')),
            'images' => \App\Http\Resources\AdImageResource::collection($this->whenLoaded('images')),
        ];
    }
}