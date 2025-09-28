<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CompatibilityRelationManager extends RelationManager
{
    protected static string $relationship = 'compatibleCarModels';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('car_model_id')
                    ->relationship('make', 'name')
                    ->required(),

                Forms\Components\TextInput::make('year_from')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(now()->year + 1),

                Forms\Components\TextInput::make('year_to')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(now()->year + 1),

                Forms\Components\Textarea::make('notes'),

                Forms\Components\Toggle::make('is_verified')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('make.name')
                    ->label('Make'),

                Tables\Columns\TextColumn::make('year_from'),

                Tables\Columns\TextColumn::make('year_to'),

                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('year_from'),
                        Forms\Components\TextInput::make('year_to'),
                        Forms\Components\Textarea::make('notes'),
                        Forms\Components\Toggle::make('is_verified')
                            ->default(false),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
