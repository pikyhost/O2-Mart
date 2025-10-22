<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseResource extends Resource
{
    /**
     * Get the Eloquent query for the resource table.
     * 
     * Applies default sorting: newest records first (descending by created_at)
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // Apply default sorting: recent to oldest (newest first)
        // Only if the model has created_at column
        if (method_exists(static::getModel(), 'getCreatedAtColumn')) {
            $query->latest();
        }
        
        return $query;
    }
}
