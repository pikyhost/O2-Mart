<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\Toggle;
use Filament\Infolists\Infolist;
use App\Filament\Resources\CountryResource\Pages\ManageCountries;
use App\Filament\Resources\CountryResource\Pages\ViewCountry;
use App\Models\Country;
use App\Traits\HasMakeCostZeroAction;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\BaseResource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CountryResource extends BaseResource
{
    use HasMakeCostZeroAction;

    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-asia-australia'; // 'heroicon-o-globe-asia-australia';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('countries');
    }

    public static function getModelLabel(): string
    {
        return __('countries');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Shipping Management');
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('countries');
    }

    public static function getLabel(): ?string
    {
        return __('countries');
    }

    public static function getPluralModelLabel(): string
    {
        return __('countries');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->columnSpanFull()
                ->label(__('name'))
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            TextInput::make('code')
                ->columnSpanFull()
                ->label(__('code'))
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            // TextInput::make('cost')
            //     ->label(__('Shipping Cost'))
            //     ->required()
            //     ->numeric()
            //     ->default(0),

            // TextInput::make('shipping_estimate_time')
            //     ->label(__('shipping_cost.shipping_estimate_time'))
            //     ->maxLength(255)
            //     ->helperText('Format: min-max (e.g., 1-3 days)'),

            Toggle::make('is_active')
                ->required()
                ->default(true),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(false)
            ->columns(self::getTableColumns())
            ->actions(self::getTableActions())
            ->bulkActions(self::getTableBulkActions());
    }

    private static function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->weight(FontWeight::Bold)
                ->label(__('id'))
                ->searchable()
                ->sortable(),

            TextColumn::make('name')
                ->label(__('name'))
                ->searchable()
                ->sortable(),

            TextColumn::make('code')
                ->badge()
                ->label(__('code'))
                ->searchable()
                ->sortable(),

            // TextColumn::make('shipping_cost')
            //     ->label(__('Shipping Cost'))
            //     ->sortable(),

            // TextColumn::make('shipping_estimate_time')
            //     ->label(__('shipping_cost.shipping_estimate_time'))
            //     ->searchable(),

            TextColumn::make('created_at')
                ->label(__('category.created_at'))
                ->dateTime()
                ->sortable(),

            TextColumn::make('updated_at')
                ->label(__('category.updated_at'))
                ->dateTime()
                ->sortable(),

            IconColumn::make('is_active')
                ->boolean()
                ->sortable(),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()->schema([
                    TextEntry::make('id')
                        ->label(__('id'))
                        ->weight(FontWeight::Bold),

                    TextEntry::make('name')
                        ->label(__('name')),

                    TextEntry::make('code')
                        ->label(__('code'))
                        ->badge(),

                    // TextEntry::make('cost')
                    //     ->label(__('Shipping Cost')),

                    // TextEntry::make('shipping_estimate_time')
                    //     ->label(__('shipping_cost.shipping_estimate_time')),

                    TextEntry::make('updated_at')
                        ->label(__('category.updated_at'))
                        ->dateTime(),
                ])->columns(2)
            ]);
    }

    private static function getTableActions(): array
    {
        return [
            EditAction::make()->color('primary'),
            DeleteAction::make(),
            ViewAction::make()
        ];
    }

    private static function getTableBulkActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make(),
                self::makeCostZeroBulkAction(),
                ExportBulkAction::make()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
            ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCountries::route('/'),
            'view'  => ViewCountry::route('/{record}'),
        ];
    }
}
