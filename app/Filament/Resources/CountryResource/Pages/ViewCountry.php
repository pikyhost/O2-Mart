<?php

namespace App\Filament\Resources\CountryResource\Pages;

use App\Filament\Resources\CountryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\Action;

class ViewCountry extends ViewRecord
{
    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

        Actions\Action::make('back')
            ->color('primary')
            ->label(__('Back'))
            ->icon(function () {
                return app()->getLocale() == 'en' ? 'heroicon-m-arrow-right' : 'heroicon-m-arrow-left';
            })
            ->iconPosition(IconPosition::After)
            ->color('gray')
            ->url(url()->previous())
            ->hidden(fn () => url()->previous() === url()->current()), // Optionally hide if same page
        ];
    }
}
