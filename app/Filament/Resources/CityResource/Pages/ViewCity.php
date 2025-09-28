<?php

namespace App\Filament\Resources\CityResource\Pages;

use App\Filament\Resources\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\IconPosition;

class ViewCity extends ViewRecord
{

    protected static string $resource = CityResource::class;

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
