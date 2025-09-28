<?php

namespace App\Traits;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;

trait HasTimestampSection
{
    /**
     * Get the timestamp section with optional additional placeholders.
     *
     * @param  array  $additionalPlaceholders  Additional placeholders to add.
     */
    public static function getTimestampSection(array $additionalPlaceholders = []): Section
    {
        $defaultPlaceholders = [
            Placeholder::make('created_at')
             ->label(__('Creation Date'))
                ->content(fn (Model $record): ?string => $record->created_at?->diffForHumans()),

            Placeholder::make('updated_at')
                ->label(__('Last Modified At'))
                ->content(fn (Model $record): ?string => $record->updated_at?->diffForHumans()),
        ];

        $placeholders = array_merge($defaultPlaceholders, $additionalPlaceholders);

        return Section::make()
            ->schema($placeholders)
            ->columnSpan(['lg' => 1])
            ->hidden(fn (?Model $record) => $record === null);
    }
}
