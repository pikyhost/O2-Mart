<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RimCountryResource\Pages;
use App\Filament\Resources\RimCountryResource\RelationManagers;
use App\Models\RimCountry;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Imports\RimCountryImporter;
use App\Exports\RimCountryExampleExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\Action;


class RimCountryResource extends Resource
{
    protected static ?string $model = RimCountry::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Rims';
    protected static ?int $navigationSort = 19;
    protected static ?string $navigationLabel = 'Rim Countries';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Country Name')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->label('Country Name')->searchable(),
                TextColumn::make('created_at')->label('Created At')->dateTime()->sortable(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Import Rim Countries')
                    ->importer(RimCountryImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Rim Countries')
                    ->modalDescription('Are you sure you want to delete all rim countries? This action cannot be undone.')
                    ->action(fn () => RimCountry::query()->delete()),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListRimCountries::route('/'),
            'create' => Pages\CreateRimCountry::route('/create'),
            'edit' => Pages\EditRimCountry::route('/{record}/edit'),
        ];
    }
}
