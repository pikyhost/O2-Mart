<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;

class CurrencyResource extends BaseResource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('Settings Management');
    }
    public static function getNavigationLabel(): string
    {
        return __('currencies');
    }

    public static function getModelLabel(): string
    {
        return __('currencies');
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('currencies');
    }

    public static function getLabel(): ?string
    {
        return __('currencies');
    }

    public static function getPluralModelLabel(): string
    {
        return __('currencies');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Section::make()->schema([
                   Forms\Components\TextInput::make('name')
                       ->required()
                       ->maxLength(255),
                   Forms\Components\TextInput::make('code')
                       ->required()
                       ->maxLength(3),
                   Forms\Components\TextInput::make('symbol')
                       ->required()
                       ->maxLength(10),
                   Forms\Components\Checkbox::make('is_active')
                       ->columnSpanFull()
                       ->default(true),
               ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('symbol')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
