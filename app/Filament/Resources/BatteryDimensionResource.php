<?php

namespace App\Filament\Resources;

use App\Exports\BatteryDimensionExampleExport;
use App\Filament\Imports\BatteryDimensionImporter;
use App\Models\BatteryDimension;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;
use App\Filament\Resources\BatteryDimensionResource\Pages;

class BatteryDimensionResource extends Resource
{
    protected static ?string $model = BatteryDimension::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-pointing-out';
    protected static ?string $navigationGroup = 'Batteries';
    protected static ?string $navigationLabel = 'Battery Dimensions';
    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('value')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('value')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Import Dimensions')
                    ->importer(BatteryDimensionImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Battery Dimensions')
                    ->modalDescription('Are you sure you want to delete all battery dimensions? This action cannot be undone.')
                    ->action(fn () => BatteryDimension::query()->delete()),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBatteryDimensions::route('/'),
            'create' => Pages\CreateBatteryDimension::route('/create'),
            'edit' => Pages\EditBatteryDimension::route('/{record}/edit'),
        ];
    }
}
