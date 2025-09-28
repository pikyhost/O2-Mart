<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Models\City;
use App\Traits\HasMakeCostZeroAction;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CityResource extends Resource
{
    use HasMakeCostZeroAction;

    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    public static function getNavigationLabel(): string
    {
        return __('cities'); // Translated to "Governorates"
    }

    public static function getModelLabel(): string
    {
        return __('cities'); // Translated to "Governorate"
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('cities'); // Translated to "Governorates"
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Shipping Management'); //Products Attributes Management
    }

    public static function getLabel(): ?string
    {
        return __('cities'); // Translated to "Governorate"
    }

    public static function getPluralModelLabel(): string
    {
        return __('cities'); // Translated to "Governorates"
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label(__('name'))
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('governorate_id')
                    ->relationship('governorate', 'name')
                    ->required()
                    ->label(__('governorate_name')),

                // Forms\Components\TextInput::make('shipping_estimate_time')
                //     ->label(__('shipping_cost.shipping_estimate_time'))
                //     ->maxLength(255)
                //     ->helperText('Format: min-max (e.g., 1-3 days)'),

                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(false)
            ->columns([
                TextColumn::make('id')
                    ->weight(FontWeight::Bold)
                    ->label(__('id'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('name')),
                Tables\Columns\TextColumn::make('governorate.name')
                    ->searchable()
                    ->label(__('governorate_name')),

                // Tables\Columns\TextColumn::make('shipping_estimate_time')
                //     ->label(__('Shipping Cost'))
                //     ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('created_at')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('updated_at')),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('governorate_id')
                    ->columnSpanFull()
                    ->label(__('governorate'))
                    ->relationship('governorate', 'name')
            ], Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('delete')),
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('delete_bulk')),
                    self::makeCostZeroBulkAction(),

                    ExportBulkAction::make()
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('name'))
                            ->weight(FontWeight::Bold)
                            ->columnSpanFull(),

                        TextEntry::make('governorate.name')
                            ->label(__('governorate_name')),

                        // TextEntry::make('shipping_estimate_time')
                        //     ->label(__('shipping_cost.shipping_estimate_time')),

                        TextEntry::make('created_at')
                            ->label(__('created_at'))
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label(__('updated_at'))
                            ->dateTime(),
                    ])->columns(2)
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCities::route('/'),
            'view'  => Pages\ViewCity::route('/{record}'),
        ];
    }
}
