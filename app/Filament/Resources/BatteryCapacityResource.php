<?php

namespace App\Filament\Resources;

use App\Exports\BatteryCapacityExampleExport;
use App\Filament\Imports\BatteryCapacityImporter;
use App\Models\BatteryCapacity;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;
use App\Filament\Resources\BatteryCapacityResource\Pages;

class BatteryCapacityResource extends BaseResource
{
    protected static ?string $model = BatteryCapacity::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationGroup = 'Batteries';
    protected static ?string $navigationLabel = 'Battery Capacities';
    protected static ?int $navigationSort = 11;

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
                    ->label('Import Capacities')
                    ->importer(BatteryCapacityImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Battery Capacities')
                    ->modalDescription('Are you sure you want to delete all battery capacities? This action cannot be undone.')
                    ->action(fn () => BatteryCapacity::query()->delete()),
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
            'index' => Pages\ListBatteryCapacities::route('/'),
            'create' => Pages\CreateBatteryCapacity::route('/create'),
            'edit' => Pages\EditBatteryCapacity::route('/{record}/edit'),
        ];
    }
}
