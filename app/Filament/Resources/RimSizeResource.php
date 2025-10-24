<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RimSizeResource\Pages;
use App\Filament\Resources\RimSizeResource\RelationManagers;
use App\Models\RimSize;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Imports\RimSizeImporter;
use App\Exports\RimSizeExampleExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\Action;

class RimSizeResource extends BaseResource
{
    protected static ?string $model = RimSize::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Rims';
    protected static ?int $navigationSort = 18;
    protected static ?string $navigationLabel = 'Rim Sizes';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('size')
                ->label('RIM Size')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('size')->label('RIM Size')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Bulk Upload')
                    ->importer(RimSizeImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Rim Sizes')
                    ->modalDescription('Are you sure you want to delete all rim sizes? This action cannot be undone.')
                    ->action(fn () => RimSize::query()->delete()),
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
            'index' => Pages\ListRimSizes::route('/'),
            'create' => Pages\CreateRimSize::route('/create'),
            'edit' => Pages\EditRimSize::route('/{record}/edit'),
        ];
    }
}
