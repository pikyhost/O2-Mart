<?php

namespace App\Filament\Resources;

use App\Filament\Imports\CarTyreSpecImporter;
use App\Filament\Resources\CarTyreSpecResource\Pages;
use App\Models\CarTyreSpec;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;

// class CarTyreSpecResource extends BaseResource
// {
//     protected static ?string $model = CarTyreSpec::class;

//     protected static ?string $navigationIcon = 'heroicon-o-truck';

//     protected static ?string $modelLabel = 'Car Tyre Specification';

//     protected static ?string $navigationLabel = 'Car Tyre Specs';

//     protected static ?string $navigationGroup = 'Products Management';
    
//     protected static ?int $navigationSort = 7;

//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
//                 Forms\Components\Section::make('Car Tyre Specifications')
//                     ->description('Enter detailed information about the car and its tyre specifications to ensure accurate matching and compatibility.')
//                     ->columns(2)
//                     ->schema([
//                         Forms\Components\TextInput::make('car_make')
//                             ->required()
//                             ->maxLength(255)
//                             ->label('Car Make'),

//                         Forms\Components\TextInput::make('car_model')
//                             ->required()
//                             ->maxLength(255)
//                             ->label('Car Model'),

//                         Forms\Components\Select::make('car_year')
//                             ->options(function () {
//                                 $years = range(date('Y'), 1900);
//                                 return array_combine($years, $years);
//                             })
//                             ->required()
//                             ->label('Manufacture Year'),

//                         Forms\Components\TextInput::make('engine_performance')
//                             ->maxLength(255)
//                             ->label('Engine Performance (e.g. 3.5L 290hp)'),

//                         Forms\Components\TextInput::make('tyre_size')
//                             ->maxLength(255)
//                             ->label('Tyre Size (e.g. 255/55R19)'),

//                         Forms\Components\Select::make('tyre_oem')
//                             ->options([
//                                 'OEM' => 'Yes (Original Equipment)',
//                                 'Not OEM' => 'No (Aftermarket)',
//                             ])
//                             ->label('Is OEM Tyre?')
//                             ->native(false),
//                     ])
//             ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 Tables\Columns\TextColumn::make('car_make')
//                     ->searchable()
//                     ->label('Car Make'),

//                 Tables\Columns\TextColumn::make('car_model')
//                     ->searchable()
//                     ->label('Car Model'),

//                 Tables\Columns\TextColumn::make('car_year')
//                     ->sortable()
//                     ->label('Year'),

//                 Tables\Columns\TextColumn::make('engine_performance')
//                     ->searchable()
//                     ->label('Engine Performance'),

//                 Tables\Columns\TextColumn::make('tyre_size')
//                     ->searchable()
//                     ->label('Tyre Size'),

//                 Tables\Columns\TextColumn::make('tyre_oem')
//                     ->searchable()
//                     ->label('OEM Tyre'),

//                 Tables\Columns\TextColumn::make('created_at')
//                     ->dateTime()
//                     ->sortable()
//                     ->toggleable(isToggledHiddenByDefault: true),

//                 Tables\Columns\TextColumn::make('updated_at')
//                     ->dateTime()
//                     ->sortable()
//                     ->toggleable(isToggledHiddenByDefault: true),
//             ])
//             ->headerActions([
//                 ImportAction::make()
//                     ->icon('heroicon-o-arrow-up-tray')
//                     ->color('danger')
//                     ->importer(CarTyreSpecImporter::class)
//             ])
//             ->filters([
//                 Tables\Filters\SelectFilter::make('car_make')
//                     ->multiple()
//                     ->label('Filter by Make')
//                     ->searchable()
//                     ->options(function () {
//                         return CarTyreSpec::query()->pluck('car_make', 'car_make')->unique();
//                     }),

//                 Tables\Filters\SelectFilter::make('car_year')
//                     ->multiple()
//                     ->label('Filter by Year')
//                     ->options(function () {
//                         return CarTyreSpec::query()->pluck('car_year', 'car_year')->unique()->sortDesc();
//                     }),
//             ])
//             ->filtersFormColumns(2)
//             ->actions([
//                 Tables\Actions\EditAction::make(),
//                 Tables\Actions\DeleteAction::make(),
//             ])
//             ->bulkActions([
//                 Tables\Actions\BulkActionGroup::make([
//                     Tables\Actions\DeleteBulkAction::make(),
//                 ]),
//             ]);
//     }

//     public static function getPages(): array
//     {
//         return [
//             'index' => Pages\ListCarTyreSpecs::route('/'),
//             'create' => Pages\CreateCarTyreSpec::route('/create'),
//             'edit' => Pages\EditCarTyreSpec::route('/{record}/edit'),
//         ];
//     }
// }
