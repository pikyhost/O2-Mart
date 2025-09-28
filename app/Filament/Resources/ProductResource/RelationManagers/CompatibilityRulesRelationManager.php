<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\Attribute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CompatibilityRulesRelationManager extends RelationManager
{
    protected static string $relationship = 'compatibilityRules';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('attribute_id')
                    ->label('Vehicle Attribute')
                    ->options(Attribute::where('is_filterable', true)->pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $attribute = Attribute::find($state);
                        if ($attribute) {
                            $set('operator', $this->getDefaultOperator($attribute->type));
                        }
                    }),

                Forms\Components\Select::make('operator')
                    ->options(function (Forms\Get $get) {
                        $attributeId = $get('attribute_id');
                        if (!$attributeId) return [];

                        $attribute = Attribute::find($attributeId);
                        return $this->getOperatorOptions($attribute->type);
                    })
                    ->required(),

                Forms\Components\Textarea::make('value')
                    ->required()
                    ->helperText(function (Forms\Get $get) {
                        $attributeId = $get('attribute_id');
                        if (!$attributeId) return '';

                        $attribute = Attribute::find($attributeId);
                        return $this->getValueHelperText($attribute->type);
                    }),
            ]);
    }

    protected function getOperatorOptions(string $type): array
    {
        return match($type) {
            'select', 'multiselect' => [
                '=' => 'Equals',
                '!=' => 'Not equals',
                'in' => 'In list',
                'not_in' => 'Not in list',
            ],
            'number', 'range' => [
                '=' => 'Equals',
                '!=' => 'Not equals',
                '>' => 'Greater than',
                '<' => 'Less than',
                'between' => 'Between',
            ],
            default => [
                '=' => 'Equals',
                '!=' => 'Not equals',
                'contains' => 'Contains',
                'not_contains' => 'Does not contain',
            ],
        };
    }

    protected function getValueHelperText(string $type): string
    {
        return match($type) {
            'select', 'multiselect' => 'For multiple values, separate with commas',
            'number', 'range' => 'For "between" operator, use format "min,max"',
            default => 'Enter comparison value',
        };
    }

    protected function getDefaultOperator(string $type): string
    {
        return match($type) {
            'select', 'multiselect' => 'in',
            default => '=',
        };
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attribute.name')
                    ->label('Attribute'),
                Tables\Columns\TextColumn::make('operator')
                    ->formatStateUsing(fn ($state) => $this->getOperatorOptions('')[$state] ?? $state),
                Tables\Columns\TextColumn::make('value'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
