<?php

namespace App\Filament\Resources;

use App\Exports\TyreModelExporter;
use App\Filament\Imports\TyreModelImporter;
use App\Filament\Resources\TyreModelResource\Pages;
use App\Filament\Resources\TyreModelResource\RelationManagers;
use App\Models\TyreModel;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TyreModelResource extends Resource
{
    protected static ?string $model = TyreModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Tyres';
    protected static ?int $navigationSort = 29;
    protected static ?string $navigationLabel = 'Tyre Model';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Model Name')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Import Models')
                    ->importer(TyreModelImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Tyre Models')
                    ->modalDescription('Are you sure you want to delete all tyre models? This action cannot be undone.')
                    ->action(fn () => TyreModel::query()->delete()),
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
            'index' => Pages\ListTyreModels::route('/'),
            'create' => Pages\CreateTyreModel::route('/create'),
            'edit' => Pages\EditTyreModel::route('/{record}/edit'),
        ];
    }
}
