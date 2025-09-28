<?php

namespace App\Filament\Resources;

use App\Filament\Imports\AutoPartCountryImporter;
use App\Models\AutoPartCountry;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ImportAction;
use App\Filament\Resources\AutoPartCountryResource\Pages;

class AutoPartCountryResource extends Resource
{
    protected static ?string $model = AutoPartCountry::class;
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'Auto Parts';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'AutoPart Countries';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Country Name')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->label('Country')->searchable()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(AutoPartCountryImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All AutoPart Countries')
                    ->modalDescription('Are you sure you want to delete all autopart countries? This action cannot be undone.')
                    ->action(fn () => AutoPartCountry::query()->delete()),
            ])
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
            'index' => Pages\ListAutoPartCountries::route('/'),
            'create' => Pages\CreateAutoPartCountry::route('/create'),
            'edit' => Pages\EditAutoPartCountry::route('/{record}/edit'),
        ];
    }
}
