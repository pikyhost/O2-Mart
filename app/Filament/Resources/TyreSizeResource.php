<?php

namespace App\Filament\Resources;

use App\Exports\TyreSizeExporter;
use App\Filament\Imports\TyreSizeImporter;
use App\Filament\Resources\TyreSizeResource\Pages;
use App\Filament\Resources\TyreSizeResource\RelationManagers;
use App\Models\TyreSize;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TyreSizeResource extends BaseResource
{
    protected static ?string $model = TyreSize::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Tyres';
    protected static ?int $navigationSort = 32;
    protected static ?string $navigationLabel = 'Tyre Size';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               TextInput::make('size')
                    ->label('Tyre Size')
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
                TextColumn::make('size')->searchable()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()

            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Import Countries')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('danger')
                    ->importer(TyreSizeImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Tyre Sizes')
                    ->modalDescription('Are you sure you want to delete all tyre sizes? This action cannot be undone.')
                    ->action(fn () => TyreSize::query()->delete()),
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
            'index' => Pages\ListTyreSizes::route('/'),
            'create' => Pages\CreateTyreSize::route('/create'),
            'edit' => Pages\EditTyreSize::route('/{record}/edit'),
        ];
    }
}
