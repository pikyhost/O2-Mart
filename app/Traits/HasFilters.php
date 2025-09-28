<?php

namespace App\Traits;

use Filament\Tables\Filters\SelectFilter;

trait HasFilters
{
    public static function filterWithCountryOrStateOrCity(): array
    {
        return [

            SelectFilter::make('country')
                ->relationship('location.country', 'name')
                ->columnSpan(['sm' => 4, 'md' => 2, 'lg' => 2, 'xl' => 1]),

            SelectFilter::make('state')
                ->relationship('location.state', 'name')
                ->columnSpan(['sm' => 4, 'md' => 2, 'lg' => 2, 'xl' => 1]),

            SelectFilter::make('city')
                ->relationship('location.city', 'name')
                ->columnSpan(['sm' => 4, 'md' => 2, 'lg' => 2, 'xl' => 1]),
        ];
    }
}
