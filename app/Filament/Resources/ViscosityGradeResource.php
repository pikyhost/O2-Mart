<?php

namespace App\Filament\Resources;

use App\Filament\Imports\ViscosityGradeImporter;
use App\Exports\ViscosityGradeExporter;
use App\Filament\Resources\ViscosityGradeResource\Pages;
use App\Models\ViscosityGrade;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ViscosityGradeResource extends Resource
{
    protected static ?string $model = ViscosityGrade::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Auto Parts';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationLabel = 'Viscosity Grades';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Viscosity Grade')
                ->required()
                ->unique(ignoreRecord: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->label('Viscosity Grade')->searchable()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(ViscosityGradeImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Viscosity Grades')
                    ->modalDescription('Are you sure you want to delete all viscosity grades? This action cannot be undone.')
                    ->action(fn () => ViscosityGrade::query()->delete()),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListViscosityGrades::route('/'),
            'create' => Pages\CreateViscosityGrade::route('/create'),
            'edit' => Pages\EditViscosityGrade::route('/{record}/edit'),
        ];
    }
}
