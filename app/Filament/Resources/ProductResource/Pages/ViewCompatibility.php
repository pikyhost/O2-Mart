<?php

// app/Filament/Resources/ProductResource/Pages/ViewCompatibility.php
namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ViewCompatibility extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->record->compatibleCarModels()->getQuery())
            ->columns([
                TextColumn::make('carMake.name')
                    ->label('Make'),
                TextColumn::make('name')
                    ->label('Model'),
                TextColumn::make('year_from')
                    ->label('Year From'),
                TextColumn::make('year_to')
                    ->label('Year To'),
            ]);
    }
}
