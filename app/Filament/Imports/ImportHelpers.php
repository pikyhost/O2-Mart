<?php

namespace App\Filament\Imports;

use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Database\Eloquent\Model;

trait ImportHelpers
{
    protected function firstOrCreateByName(string $class, ?string $name, ?int $parentId = null): ?Model
    {
        if (!$name) return null;

        $attrs = ['name' => trim($name)];
        if (!is_null($parentId)) $attrs['parent_id'] = $parentId;

        $defaults = [];
        
        // Add slug if model supports it
        if (method_exists($class, 'sluggable')) {
            $defaults['slug'] = SlugService::createSlug($class, 'slug', $name);
        }
        
        // Add status field based on model
        if ($class === \App\Models\Category::class) {
            $defaults['is_active'] = true;
        } elseif ($class === \App\Models\RimAttribute::class) {
            $defaults['car_make_id'] = 1; // Default car make
            $defaults['car_model_id'] = 1; // Default car model
            $defaults['model_year'] = 2024; // Default year
        } elseif (in_array('is_published', (new $class)->getFillable())) {
            $defaults['is_published'] = true;
        }
        
        return $class::firstOrCreate($attrs, $defaults);
    }

    protected function parseBool($state, bool $default = false): bool
    {
        if (is_bool($state)) return $state;
        $value = strtolower(trim((string) $state));
        return match ($value) {
            '1','true','yes','y','on' => true,
            '0','false','no','n','off','' => false,
            default => $default,
        };
    }

    protected function whenFilled(string $key, callable $cb): void
    {
        $val = data_get($this->data, $key);
        if ($val !== null && $val !== '') $cb($val);
    }
}
