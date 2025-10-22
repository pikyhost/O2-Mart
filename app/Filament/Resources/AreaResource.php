<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AreaResource\Pages;
use App\Models\Area;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AreaResource extends BaseResource
{
    protected static ?string $model = Area::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function getNavigationGroup(): ?string
    {
        return __('Shipping Management'); //Products Attributes Management
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->orderBy('id', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Section::make()->schema([
                   Forms\Components\Select::make('city_id')
                       ->relationship('city', 'name')
                       ->searchable()
                       ->preload()
                       ->required(),

                   Forms\Components\TextInput::make('name')
                       ->required()
                       ->unique(ignoreRecord: true)
                       ->maxLength(255),

                   Forms\Components\TextInput::make('shipping_cost')
                       ->label('Additional Shipping Cost')
                       ->numeric()
                       ->helperText('Additional cost added to base shipping calculation (leave empty for no additional cost)'),

                //    Forms\Components\TextInput::make('shipping_estimate_time')
                //        ->required()
                //        ->maxLength(10)
                //        ->helperText('Format: min-max (e.g., 1-3 days)'),

                   Forms\Components\Toggle::make('is_active')
                       ->required()
                       ->default(true),
                       
                   Forms\Components\Toggle::make('is_remote')
                       ->label('Is Remote Area')
                       ->helperText('Mark if this area is considered remote for shipping purposes')
                       ->default(false),
               ])
            ]);
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

                Tables\Columns\TextColumn::make('city.name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('shipping_cost')
                    ->label('Additional Shipping Cost')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state > 0 ? '+' . number_format($state, 2) . ' AED' : 'No additional cost'),

                // Tables\Columns\TextColumn::make('shipping_estimate_time')
                //     ->label('Est. Days'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_remote')
                    ->label('Remote')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->default(true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city_id')
                    ->columnSpanFull()
                    ->label(__('City'))
                    ->relationship('city', 'name')
            ], Tables\Enums\FiltersLayout::AboveContent)
            ->defaultSort('id', 'desc')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAreas::route('/'),
        ];
    }
}
