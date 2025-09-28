<?php

namespace App\Filament\Resources;

use App\Exports\TyreCountryExporter;
use App\Filament\Imports\TyreCountryImporter;
use App\Filament\Resources\TyreCountryResource\Pages;
use App\Filament\Resources\TyreCountryResource\RelationManagers;
use App\Models\TyreCountry;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TyreCountryResource extends Resource
{
    protected static ?string $model = TyreCountry::class;

    protected static ?string $navigationGroup = 'Tyres';
    protected static ?int $navigationSort = 31;
    protected static ?string $navigationLabel = 'Tyre Country';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Country Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()

            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Import Countries')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('danger')
                    ->importer(TyreCountryImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Tyre Countries')
                    ->modalDescription('Are you sure you want to delete all tyre countries? This action cannot be undone.')
                    ->action(fn () => TyreCountry::query()->delete()),
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
            'index' => Pages\ListTyreCountries::route('/'),
            'create' => Pages\CreateTyreCountry::route('/create'),
            'edit' => Pages\EditTyreCountry::route('/{record}/edit'),
        ];
    }
}
