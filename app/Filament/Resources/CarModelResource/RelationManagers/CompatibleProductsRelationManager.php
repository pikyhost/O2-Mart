<?php

namespace App\Filament\Resources\CarModelResource\RelationManagers;

use App\Models\Product;
use App\Services\CompatibilityService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompatibleProductsRelationManager extends RelationManager
{
    protected static ?string $title = 'Compatible Products';
    protected static string $relationship = 'compatibleProducts'; // No direct relationship

    public function form(Form $form): Form
    {
        return $form->schema([
            // No form needed for this display-only relation manager
        ]);
    }

    public function table(Table $table): Table
    {
        $compatibilityService = app(CompatibilityService::class);
        $carModel = $this->getOwnerRecord();

        return $table
            ->heading('Compatible Products (Automatically Determined)')
            ->description('These products match this vehicle based on compatibility rules')
            ->query(
                Product::query()
                    ->whereHas('compatibilityRules')
                    ->with('compatibilityRules.attribute')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('compatibilityRulesCount')
                    ->label('Matching Rules')
                    ->counts('compatibilityRules'),

                Tables\Columns\TextColumn::make('stock_status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('categories', 'name'),

                Tables\Filters\TernaryFilter::make('in_stock')
                    ->placeholder('All')
                    ->trueLabel('In stock only')
                    ->queries(
                        true: fn (Builder $query) => $query->where('stock_status', 'in_stock'),
                        false: fn (Builder $query) => $query->where('stock_status', '!=', 'in_stock'),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('view_rules')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->modalHeading(fn (Product $record) => "Compatibility Rules: {$record->name}")
                    ->modalContent(function (Product $record) use ($carModel, $compatibilityService) {
                        //  Load rules directly without relationship
                        $rules = \App\Models\CompatibilityRule::where('product_id', $record->id)
                            ->with('attribute')
                            ->get();

                        $evaluationResults = [];

                        foreach ($rules as $rule) {
                            //  Load attribute value directly from the pivot table
                            $carModelValue = $carModel->attributes()
                                ->where('attribute_id', $rule->attribute_id)
                                ->first()
                                ?->pivot
                                ?->value;

                            $evaluationResults[] = [
                                'attribute' => $rule->attribute->name,
                                'rule' => $this->formatRule($rule),
                                'value' => $carModelValue ?? 'N/A',
                                'matches' => $carModelValue ? $compatibilityService->evaluateRule($rule, $carModelValue) : false,
                            ];
                        }

                        return view('filament.compatibility-rules', [
                            'rules' => $evaluationResults,
                            'product' => $record,
                            'carModel' => $carModel,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
            ])
            ->bulkActions([]);
    }

    protected function formatRule($rule): string
    {
        return match($rule->operator) {
            '=' => "Must be {$rule->value}",
            '!=' => "Must not be {$rule->value}",
            '>' => "Must be greater than {$rule->value}",
            '<' => "Must be less than {$rule->value}",
            'in' => "Must be one of: {$rule->value}",
            'not_in' => "Must not be any of: {$rule->value}",
            'between' => "Must be between {$rule->value}",
            default => $rule->operator.' '.$rule->value,
        };
    }
}
