<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GovernorateResource\Pages;
use App\Models\Governorate;
use App\Traits\HasMakeCostZeroAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GovernorateResource extends Resource
{
    use HasMakeCostZeroAction;

    protected static ?string $model = Governorate::class;
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    public static function getNavigationLabel(): string
    {
        return __('governorates'); // Translated to "Governorates"
    }

    public static function getModelLabel(): string
    {
        return __('governorate'); // Translated to "Governorate"
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('governorates'); // Translated to "Governorates"
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Shipping Management'); //Products Attributes Management
    }

    public static function getLabel(): ?string
    {
        return __('governorate'); // Translated to "Governorate"
    }

    public static function getPluralModelLabel(): string
    {
        return __('governorates'); // Translated to "Governorates"
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255)
                    ->label(__('name')),

                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->required()
                    ->label(__('country_name')),

                // Forms\Components\TextInput::make('shipping_cost')
                //     ->label(__('Shipping Cost'))
                //     ->required()
                //     ->numeric()
                //     ->default(0),

                // Forms\Components\TextInput::make('shipping_estimate_time')
                //     ->label(__('shipping_cost.shipping_estimate_time'))
                //     ->maxLength(255)
                //     ->helperText('Format: min-max (e.g., 1-3 days)'),

                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->weight(FontWeight::Bold)
                    ->label(__('id'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('name')),
                Tables\Columns\TextColumn::make('country.name')
                    ->searchable()
                    ->label(__('country_name')),

                // Tables\Columns\TextColumn::make('shipping_cost')
                //     ->label(__('Shipping Cost'))
                //     ->sortable(),

                // Tables\Columns\TextColumn::make('shipping_estimate_time')
                //     ->label(__('shipping_cost.shipping_estimate_time'))
                //     ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('updated_at')),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

            ])
            ->recordUrl(false)
            ->filters([
                Tables\Filters\SelectFilter::make('country_id')
                    ->columnSpanFull()
                    ->label(__('country'))
                    ->relationship('country', 'name')
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
                Section::make()->schema([
                    TextEntry::make('name')
                        ->label(__('name')),

                    TextEntry::make('cost')
                        ->label(__('Shipping Cost')),

                    TextEntry::make('shipping_estimate_time')
                        ->label(__('shipping_cost.shipping_estimate_time')),

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
            'index' => Pages\ManageGovernorates::route('/'),
            'view'  => Pages\ViewGovernorate::route('/{record}'),
        ];
    }
}
