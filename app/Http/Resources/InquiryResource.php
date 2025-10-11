<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InquiryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'type_label' => $this->type_label,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'priority' => $this->priority,
            'page_source' => $this->page_source,

            // Customer Information
            'customer' => [
                'full_name' => $this->full_name,
                'phone_number' => $this->phone_number,
                'email' => $this->email,
            ],

            // Vehicle Information
            'vehicle' => $this->when($this->hasVehicleInfo(), [
                'make' => $this->car_make,
                'model' => $this->car_model,
                'year' => $this->car_year,
                'vin_chassis_number' => $this->vin_chassis_number,
            ]),

            // Inquiry Details
            'details' => [
                'required_parts' => $this->required_parts,
                'required_parts_string' => $this->getRequiredPartsString(),
                'quantity' => $this->quantity,
                'battery_specs' => $this->battery_specs,
                'rim_size' => $this->whenLoaded('rimSize', fn() => $this->rimSize->size),
                'front_width' => $this->front_width,
                'front_height' => $this->front_height,
                'front_diameter' => $this->front_diameter,
                'rear_tyres' => $this->rear_tyres,
                'description' => $this->description,
            ],

            // Files
            'files' => [
                'car_license_photos' => $this->car_license_photos_urls,
                'part_photos' => $this->part_photos_urls,
                'has_files' => $this->hasFiles(),
            ],

            // Administrative
            'quoted_price' => $this->quoted_price,
            'quoted_at' => $this->quoted_at?->toISOString(),
            'is_quoted' => $this->isQuoted(),
            'assigned_user' => $this->whenLoaded('assignedUser', fn() => [
                'id' => $this->assignedUser->id,
                'name' => $this->assignedUser->name,
            ]),

            // Timestamps
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }

    private function hasVehicleInfo(): bool
    {
        return $this->car_make || $this->car_model || $this->car_year || $this->vin_chassis_number;
    }
}
