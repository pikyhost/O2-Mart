<?php

namespace App\Filament\Resources;

use App\Filament\Imports\BatteryCountryImporter;
use App\Filament\Resources\BatteryCountryResource\Pages;
use App\Models\BatteryCountry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ImportAction;

class BatteryCountryResource extends Resource
{
    protected static ?string $model = BatteryCountry::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'Batteries';
    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('code')
                ->maxLength(10),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('code')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->headerActions([
            ImportAction::make()
                ->label('Import Battery Countries')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->importer(BatteryCountryImporter::class),
            Tables\Actions\Action::make('deleteAll')
                ->label('Delete All Records')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Delete All Battery Countries')
                ->modalDescription('Are you sure you want to delete all battery countries? This action cannot be undone.')
                ->action(fn () => BatteryCountry::query()->delete()),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBatteryCountries::route('/'),
            'create' => Pages\CreateBatteryCountry::route('/create'),
            'edit' => Pages\EditBatteryCountry::route('/{record}/edit'),
        ];
    }
}
