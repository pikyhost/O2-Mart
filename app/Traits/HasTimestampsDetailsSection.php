<?php

namespace App\Traits;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

trait HasTimestampsDetailsSection
{
    private static function getTimestampsDetailsSection(): Section
    {
        return Section::make(__('__Timestamps Details__'))
            ->visible(fn ($record) => auth()->user()->can('view_timestamps_details_book'))
            ->schema([
                TextEntry::make('created_at')
                    ->inlineLabel()
                    ->label(__('__Date Added to Library__'))
                    ->dateTime(__('__D, M j, Y \a\t g:i A__'))
                    ->visible(fn ($record) => auth()->user()->can('view_timestamps_details_book')),

                TextEntry::make('updated_at')
                    ->inlineLabel()
                    ->label(__('__Last Modified At__'))
                    ->dateTime(__('__D, M j, Y \a\t g:i A__'))
                    ->visible(fn ($record) => auth()->user()->can('view_timestamps_details_book')),

                TextEntry::make('deleted_at')
                    ->inlineLabel()
                    ->label(__('__Deleted At__'))
                    ->badge()
                    ->color('danger')
                    ->dateTime(__('__D, M j, Y \a\t g:i A__'))
                    ->visible(fn ($record) => $record->trashed() && auth()->user()->can('view_timestamps_details_book')),
            ]);
    }
}
