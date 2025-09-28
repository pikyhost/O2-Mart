<?php

namespace App\Filament\Resources\CarMakeResource\RelationManagers;

use App\Filament\Resources\CarModelResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ModelsRelationManager extends RelationManager
{
    protected static string $relationship = 'models';

    public function form(Form $form): Form
    {
        return $form
            ->schema(CarModelResource::form($form)->getComponents());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('year_from'),

                Tables\Columns\TextColumn::make('year_to'),

                Tables\Columns\TextColumn::make('fuel_type')
                    ->badge(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
