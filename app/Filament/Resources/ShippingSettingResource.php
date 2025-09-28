<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingSettingResource\Pages;
use App\Models\ShippingSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShippingSettingResource extends Resource
{
    protected static ?string $model = ShippingSetting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Shipping Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('installation_fee')
                    ->numeric()
                    ->label('Installation Fee')
                    ->required(),
                Forms\Components\TextInput::make('extra_per_kg')
                    ->numeric()
                    ->label('Extra per KG')
                    ->required(),
                Forms\Components\TextInput::make('fuel_percent')
                    ->numeric()
                    ->label('Fuel Percent')
                    ->step(0.01)
                    ->required(),
                Forms\Components\TextInput::make('packaging_fee')
                    ->numeric()
                    ->label('Packaging Fee')
                    ->required(),
                Forms\Components\TextInput::make('epg_percent')
                    ->numeric()
                    ->label('EPG Percent')
                    ->step(0.01)
                    ->required(),
                Forms\Components\TextInput::make('epg_min')
                    ->numeric()
                    ->label('EPG Minimum')
                    ->required(),
                Forms\Components\TextInput::make('vat_percent')
                    ->numeric()
                    ->label('VAT Percent')
                    ->step(0.01)
                    ->required(),
                Forms\Components\TextInput::make('volumetric_divisor')
                    ->numeric()
                    ->label('Volumetric Divisor')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('installation_fee')
                    ->label('Installation Fee')
                    ->money('AED')
                    ->sortable(),
                Tables\Columns\TextColumn::make('extra_per_kg')
                    ->label('Extra per KG')
                    ->money('AED')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fuel_percent')
                    ->label('Fuel %')
                    ->formatStateUsing(fn ($state) => ($state * 100) . '%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('packaging_fee')
                    ->label('Packaging Fee')
                    ->money('AED')
                    ->sortable(),
                Tables\Columns\TextColumn::make('epg_percent')
                    ->label('EPG %')
                    ->formatStateUsing(fn ($state) => ($state * 100) . '%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('vat_percent')
                    ->label('VAT %')
                    ->formatStateUsing(fn ($state) => ($state * 100) . '%')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShippingSettings::route('/'),
            'edit' => Pages\EditShippingSetting::route('/{record}/edit'),
        ];
    }
}