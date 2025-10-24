<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Inquiry;

class StoreInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // vin or upload car license photos
        return [
            'type' => ['required', Rule::in(array_keys(Inquiry::TYPES))],
            'priority' => ['sometimes', Rule::in(array_keys(Inquiry::PRIORITIES))],

            // Customer Information
            'full_name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
            'email' => 'required|email|max:200',

            // Vehicle Information (backend field names) - accept both string names and integer IDs
            'car_make' => 'nullable',
            'car_model' => 'nullable',
            'car_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            
            // Vehicle Information (frontend field names - will be mapped) - accept both string names and integer IDs
            'make' => 'nullable',
            'model' => 'nullable',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'vin_chassis_number' => [
                'nullable',
                'string',
                'max:50',
                'alpha_num',
                function ($attribute, $value, $fail) {
                    if (!empty($value) && $this->hasFile('car_license_photos')) {
                        $fail('You cannot provide both VIN/Chassis number and car license photos.');
                    }
                },
            ],

            // Inquiry Details
            'required_parts' => 'nullable|array|max:10',
            'required_parts.*.part' => 'required_with:required_parts|string|max:100',
            'required_parts.*.quantity' => 'required_with:required_parts|integer|min:1|max:1000',
            'quantity' => 'nullable|integer|min:1|max:1000',
            'quantities' => 'nullable|array|max:10',
            'quantities.*' => 'integer|min:1|max:1000',
            'battery_specs' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',

            'rim_size' => 'nullable|string|max:50',
            'rim_size_id' => 'nullable|exists:rim_sizes,id',
            'front_width' => ['nullable', 'string'],
            'front_height' => ['nullable', 'string'],
            'front_diameter' => ['nullable', 'string'],
            
            // Frontend tire dimension fields (will be mapped to front_*)
            'width' => ['nullable', 'string'],
            'height' => ['nullable', 'string'],
            'diameter' => ['nullable', 'string'],

            // ✅ Rear Tyres
            'rear_tyres' => 'nullable|array|max:4',
            'rear_tyres.*.width' => 'required_with:rear_tyres|string|max:10',
            'rear_tyres.*.height' => 'required_with:rear_tyres|string|max:10',
            'rear_tyres.*.diameter' => 'required_with:rear_tyres|string|max:10',
            'rear_tyres.*.quantity' => 'required_with:rear_tyres|integer|min:1|max:10',

            // File uploads
            'car_license_photos' => 'nullable|array|max:5',
            'car_license_photos.*' => 'file|mimes:jpeg,jpg,png,pdf,webp|max:10240',
            'part_photos' => 'nullable|array|max:10',
            'part_photos.*' => 'file|mimes:jpeg,jpg,png,pdf,webp|max:10240',

            // Source tracking
            'source' => 'nullable|string|max:50',
            'page_source' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.regex' => 'Please enter a valid phone number.',
            'email.email' => 'Please enter a valid email address.',
            'car_year.min' => 'Car year must be 1900 or later.',
            'car_year.max' => 'Car year cannot be more than next year.',
            'vin_chassis_number.alpha_num' => 'VIN/Chassis number must contain only letters and numbers.',
            'vin_chassis_number' => 'You cannot provide both VIN/Chassis number and car license photos.',
            'car_license_photos' => 'You cannot provide both car license photos and VIN/Chassis number.',
            'required_parts.max' => 'Maximum 10 parts can be requested.',
            'car_license_photos.max' => 'Maximum 5 car license photos allowed.',
            'part_photos.max' => 'Maximum 10 part photos allowed.',
            'car_license_photos.*.max' => 'Each photo must be less than 10MB.',
            'part_photos.*.max' => 'Each photo must be less than 10MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Map frontend field names to backend field names
        $mappedData = [
            'source' => $this->source ?? 'api',
            'priority' => $this->priority ?? 'medium',
        ];

        // Handle type field mapping for frontend variations
        if ($this->has('type')) {
            $type = $this->input('type');
            // Map frontend type variations to backend types
            $typeMapping = [
                'Tires by Size' => 'tires',
                'tires_by_size' => 'tires',
                'tires-by-size' => 'tires',
                'tires-by-car' => 'tires',
                'Auto Parts' => 'auto_parts',
                'Battery' => 'battery',
                'Rims' => 'rims',
                'Tires' => 'tires',
            ];
            
            if (isset($typeMapping[$type])) {
                $mappedData['type'] = $typeMapping[$type];
            }
        }

        // Handle vehicle info field mapping (from 'make' field)
        if ($this->has('make')) {
            $makeValue = $this->input('make');
            // If make is numeric (ID), look up the name
            if (is_numeric($makeValue)) {
                $carMake = \App\Models\CarMake::find($makeValue);
                $mappedData['car_make'] = $carMake?->name ?? $makeValue;
            } else {
                $mappedData['car_make'] = $makeValue;
            }
        }
        
        // Handle vehicle info field mapping (from 'car_make' field)
        if ($this->has('car_make') && !isset($mappedData['car_make'])) {
            $makeValue = $this->input('car_make');
            // If make is numeric (ID), look up the name
            if (is_numeric($makeValue)) {
                $carMake = \App\Models\CarMake::find($makeValue);
                $mappedData['car_make'] = $carMake?->name ?? $makeValue;
            }
        }
        
        // Handle vehicle info field mapping (from 'model' field)
        if ($this->has('model')) {
            $modelValue = $this->input('model');
            // If model is numeric (ID), look up the name
            if (is_numeric($modelValue)) {
                $carModel = \App\Models\CarModel::find($modelValue);
                $mappedData['car_model'] = $carModel?->name ?? $modelValue;
            } else {
                $mappedData['car_model'] = $modelValue;
            }
        }
        
        // Handle vehicle info field mapping (from 'car_model' field)
        if ($this->has('car_model') && !isset($mappedData['car_model'])) {
            $modelValue = $this->input('car_model');
            // If model is numeric (ID), look up the name
            if (is_numeric($modelValue)) {
                $carModel = \App\Models\CarModel::find($modelValue);
                $mappedData['car_model'] = $carModel?->name ?? $modelValue;
            }
        }
        
        if ($this->has('year')) {
            $mappedData['car_year'] = $this->input('year');
        }

        // Handle tire dimensions field mapping
        if ($this->has('width')) {
            $mappedData['front_width'] = $this->input('width');
        }
        if ($this->has('height')) {
            $mappedData['front_height'] = $this->input('height');
        }
        if ($this->has('diameter')) {
            $mappedData['front_diameter'] = $this->input('diameter');
        }

        // Handle old format: Convert required_parts array of strings + quantities array to new format
        if ($this->has('required_parts') && is_array($this->input('required_parts'))) {
            $requiredParts = $this->input('required_parts');
            $quantities = $this->input('quantities', []);
            
            // Check if it's the old format (array of strings)
            if (!empty($requiredParts) && is_string($requiredParts[0] ?? null)) {
                // Convert to new format: array of objects with 'part' and 'quantity'
                $newRequiredParts = [];
                foreach ($requiredParts as $index => $part) {
                    if (!empty($part)) {
                        $newRequiredParts[] = [
                            'part' => $part,
                            'quantity' => isset($quantities[$index]) ? (int)$quantities[$index] : 1
                        ];
                    }
                }
                $mappedData['required_parts'] = $newRequiredParts;
            }
            // If it's already in the new format (array of objects), leave it as is
        }

        // CRITICAL FIX: Handle quantity based on the format it's sent
        
        // 1. If quantity is explicitly provided as a direct field
        if ($this->has('quantity') && $this->input('quantity') !== null) {
            $quantityValue = $this->input('quantity');
            $mappedData['quantity'] = is_numeric($quantityValue) ? (int)$quantityValue : 1;
        }
        // 2. If quantity is inside required_parts array (Rims case)
        elseif (isset($mappedData['required_parts']) && is_array($mappedData['required_parts']) && !empty($mappedData['required_parts'])) {
            // Extract quantity from first item in required_parts
            $firstPart = $mappedData['required_parts'][0];
            if (isset($firstPart['quantity'])) {
                $mappedData['quantity'] = (int)$firstPart['quantity'];
            }
        }
        // 3. Fallback: Handle quantities array for backward compatibility
        elseif ($this->has('quantities') && is_array($this->input('quantities'))) {
            $quantities = $this->input('quantities');
            $mappedData['quantity'] = !empty($quantities) && is_numeric($quantities[0]) ? (int)$quantities[0] : 1;
        }

        $this->merge($mappedData);
    }
}
