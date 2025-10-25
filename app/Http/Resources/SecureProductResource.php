<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SecureProductResource extends JsonResource
{
    public function toArray($request)
    {
        // Only return essential data, hide timestamps and internal metadata
        return [
            'id' => $this->id,
            'name' => $this->name ?? $this->title,
            'price' => $this->price_vat_inclusive ?? $this->regular_price,
            'discounted_price' => $this->discounted_price,
            'description' => $this->description,
            'feature_image' => $this->getFeatureImageUrl(),
            'average_rating' => $this->average_rating ?? 5,
            'brand' => $this->getBrandData(),
            // Hide: created_at, updated_at, meta_title, meta_description, alt_text, etc.
        ];
    }

    private function getFeatureImageUrl()
    {
        return $this->tyre_feature_image_url ?? 
               $this->battery_feature_image_url ?? 
               $this->rim_feature_image_url ?? 
               $this->auto_part_feature_image_url ?? null;
    }

    private function getBrandData()
    {
        return $this->tyreBrand?->only(['name']) ?? 
               $this->batteryBrand?->only(['value']) ?? 
               $this->rimBrand?->only(['name']) ?? 
               $this->autoPartBrand?->only(['name']) ?? null;
    }
}