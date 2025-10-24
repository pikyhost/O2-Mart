<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RimAttributeResource\Pages;
use App\Filament\Resources\RimAttributeResource\RelationManagers;
use App\Models\RimAttribute;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\Action;
use App\Filament\Imports\RimAttributeImporter;
use App\Exports\RimAttributeExampleExport;
use Filament\Forms\Get;
use Maatwebsite\Excel\Facades\Excel;


class RimAttributeResource extends BaseResource
{
    protected static ?string $model = RimAttribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Rims';
    protected static ?int $navigationSort = 21;
    protected static ?string $navigationLabel = 'Rim Attributes';

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

                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->label('Bulk Upload')
                    ->color('danger')
                    ->importer(RimAttributeImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Rim Attributes')
                    ->modalDescription('Are you sure you want to delete all rim attributes? This action cannot be undone.')
                    ->action(fn () => RimAttribute::query()->delete()),
            ])

            ->columns([
                Tables\Columns\TextColumn::make('make.name')->label('Car Make'),
                Tables\Columns\TextColumn::make('model.name')->label('Car Model'),
                Tables\Columns\TextColumn::make('model_year'),
                Tables\Columns\TextColumn::make('name')->label('Attribute Name'),

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
            'index' => Pages\ListRimAttributes::route('/'),
            'create' => Pages\CreateRimAttribute::route('/create'),
            'edit' => Pages\EditRimAttribute::route('/{record}/edit'),
        ];
    }
}
