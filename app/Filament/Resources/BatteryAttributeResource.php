<?php

namespace App\Filament\Resources;

use App\Exports\BatteryAttributeExampleExport;
use App\Filament\Imports\BatteryAttributeImporter;
use App\Filament\Resources\BatteryAttributeResource\Pages;
use App\Filament\Resources\BatteryAttributeResource\RelationManagers;
use App\Models\BatteryAttribute;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ImportAction; 
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maatwebsite\Excel\Facades\Excel;

class BatteryAttributeResource extends Resource
{
    protected static ?string $model = BatteryAttribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Batteries';
    protected static ?int $navigationSort = 9;
    protected static ?string $navigationLabel = 'Battery Attributes';


    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('car_make_id')
                ->label('Car Make')
                ->relationship('make', 'name')
                ->reactive()
                ->required(),

            Select::make('car_model_id')
                ->label('Car Model')
                ->options(fn (Get $get) =>
                    \App\Models\CarModel::where('car_make_id', $get('car_make_id'))->pluck('name', 'id')
                )
                ->reactive()
                ->required(),

            Select::make('model_year')
                ->label('Model Year')
                ->options(function (Get $get) {
                    $model = \App\Models\CarModel::find($get('car_model_id'));
                    if (!$model) return [];

                    return array_combine(
                        range($model->year_from, $model->year_to),
                        range($model->year_from, $model->year_to)
                    );
                })
                ->required(),

            TextInput::make('name')
                ->label('Attribute Name')
                ->required(),
        ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('make.name')
                    ->label('Car Make')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('model.name')
                    ->label('Car Model')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('model_year')
                    ->label('Year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Attribute Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->headerActions([
                ImportAction::make()
                    ->label('Import Attributes')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('danger')
                    ->importer(BatteryAttributeImporter::class)
                    ->options([
                        'maxFileSize' => '100MB'
                    ]),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Battery Attributes')
                    ->modalDescription('Are you sure you want to delete all battery attributes? This action cannot be undone.')
                    ->action(fn () => BatteryAttribute::query()->delete()),
            ])

            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBatteryAttributes::route('/'),
            'create' => Pages\CreateBatteryAttribute::route('/create'),
            'edit' => Pages\EditBatteryAttribute::route('/{record}/edit'),
        ];
    }
}
