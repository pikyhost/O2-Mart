<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TyreBrandResource\Pages;
use App\Filament\Resources\TyreBrandResource\RelationManagers;
use App\Models\TyreBrand;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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

class TyreBrandResource extends Resource
{
    protected static ?string $model = TyreBrand::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Tyres';
    protected static ?int $navigationSort = 30;
    protected static ?string $navigationLabel = 'Tyre Brand';


    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Brand Name')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
        
            SpatieMediaLibraryFileUpload::make('logo')
                ->label('Brand Logo')
                ->collection('logo')
                ->image()
                ->maxSize(2048)
                ->preserveFilenames(),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (TyreBrand $record) {
                        $record->tyres()->update(['tyre_brand_id' => null]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->before(function ($records) {
                        foreach ($records as $record) {
                            $record->tyres()->update(['tyre_brand_id' => null]);
                        }
                    }),
            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Import Brands')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('danger')
                    ->importer(\App\Filament\Imports\TyreBrandImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Tyre Brands')
                    ->modalDescription('Are you sure you want to delete all tyre brands? This action cannot be undone.')
                    ->action(fn () => TyreBrand::query()->delete()),
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
            'index' => Pages\ListTyreBrands::route('/'),
            'create' => Pages\CreateTyreBrand::route('/create'),
            'edit' => Pages\EditTyreBrand::route('/{record}/edit'),
        ];
    }
}
