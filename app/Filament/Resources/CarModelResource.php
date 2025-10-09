<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarModelResource\Pages;
use App\Filament\Resources\CarModelResource\RelationManagers;
use App\Filament\Imports\CarModelImporter;
use App\Models\CarModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CarModelResource extends Resource
{
    protected static ?string $model = CarModel::class;

    protected static ?string $navigationIcon = 'elusive-car';

    public static function getNavigationGroup(): ?string {return 'Vehicle Data';}
    public static function getNavigationSort(): ?int  { return 20;  }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Select::make('car_make_id')
                            ->relationship('make', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                    ])->columns(2),

                Forms\Components\Section::make('Model Details')
                    ->schema([
                        Forms\Components\TextInput::make('year_from')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(now()->year + 1)
                            ->required(),

                        Forms\Components\TextInput::make('year_to')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(now()->year + 1)
                            ->gt('year_from'),

                        // Forms\Components\TextInput::make('generation')
                        //     ->maxLength(100),

                        // Forms\Components\Select::make('fuel_type')
                        //     ->options([
                        //         'petrol' => 'Petrol',
                        //         'diesel' => 'Diesel',
                        //         'electric' => 'Electric',
                        //         'hybrid' => 'Hybrid',
                        //     ]),

                        // Forms\Components\TextInput::make('engine_size')
                        //     ->numeric()
                        //     ->minValue(0)
                        //     ->suffix('L'),
                    ])->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('make.name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('year_from')
                    ->sortable(),

                Tables\Columns\TextColumn::make('year_to')
                    ->sortable(),

                // Tables\Columns\TextColumn::make('fuel_type')
                //     ->badge()
                //     ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('car_make_id')
                    ->relationship('make', 'name')
                    ->label('Make'),

                // Tables\Filters\SelectFilter::make('fuel_type')
                //     ->options([
                //         'petrol' => 'Petrol',
                //         'diesel' => 'Diesel',
                //         'electric' => 'Electric',
                //         'hybrid' => 'Hybrid',
                //     ]),

                Tables\Filters\Filter::make('years')
                    ->form([
                        Forms\Components\TextInput::make('year_from')
                            ->numeric()
                            ->placeholder('From year'),
                        Forms\Components\TextInput::make('year_to')
                            ->numeric()
                            ->placeholder('To year'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['year_from'],
                                fn (Builder $query, $year): Builder => $query->where('year_from', '>=', $year),
                            )
                            ->when(
                                $data['year_to'],
                                fn (Builder $query, $year): Builder => $query->where('year_to', '<=', $year),
                            );
                    }),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(CarModelImporter::class),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttributesRelationManager::class,
            RelationManagers\CompatibleProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarModels::route('/'),
            'create' => Pages\CreateCarModel::route('/create'),
            'edit' => Pages\EditCarModel::route('/{record}/edit'),
        ];
    }
}
