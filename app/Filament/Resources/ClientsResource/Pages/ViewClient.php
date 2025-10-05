<?php

namespace App\Filament\Resources\ClientsResource\Pages;

use App\Filament\Resources\ClientsResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Actions;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make('Client Details')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('Basic Info')
                            ->schema([
                                Infolists\Components\TextEntry::make('id')->label('Client ID'),
                                Infolists\Components\TextEntry::make('first_name'),
                                Infolists\Components\TextEntry::make('last_name'),
                                Infolists\Components\TextEntry::make('display_name')->label('Display Name'),
                                Infolists\Components\TextEntry::make('email'),
                                Infolists\Components\TextEntry::make('phone')->label('Mobile Number'),
                            ])
                            ->columns(2), 

                        Infolists\Components\Tabs\Tab::make('Addresses')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('addresses')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('label')
                                            ->label('Label')
                                            ->suffix(fn ($record) => $record->is_primary ? ' ✅ Primary' : null)
                                            ->badge(fn ($record) => $record->is_primary)
                                            ->color(fn ($record) => $record->is_primary ? 'success' : 'gray'),

                                        Infolists\Components\TextEntry::make('full_name')->label('Full Name'),
                                        Infolists\Components\TextEntry::make('phone')->label('Phone'),
                                        Infolists\Components\TextEntry::make('full_location')->label('Location'),
                                        Infolists\Components\TextEntry::make('address_line_1')->label('Address Line 1'),
                                        Infolists\Components\TextEntry::make('address_line_2')->label('Address Line 2'),
                                    ])
                                    ->label('Client Addresses'),
                            ])
                            ->columns(2),
                            
                        Infolists\Components\Tabs\Tab::make('Garage')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('userVehicles')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('make.name')
                                            ->label('Make'),
                                        Infolists\Components\TextEntry::make('model.name')
                                            ->label('Model'),
                                        Infolists\Components\TextEntry::make('car_year')
                                            ->label('Year'),
                                        Infolists\Components\TextEntry::make('vin')
                                            ->label('VIN')
                                            ->placeholder('Not specified'),
                                        Infolists\Components\TextEntry::make('mileage')
                                            ->label('Mileage')
                                            ->placeholder('Not specified')
                                            ->suffix(' km'),
                                        Infolists\Components\TextEntry::make('created_at')
                                            ->label('Added')
                                            ->dateTime(),
                                        Infolists\Components\TextEntry::make('updated_at')
                                            ->label('Last Modified At')
                                            ->dateTime(),
                                    ])
                                    ->label('Client Vehicles'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(), 
            ]);
    }
}
