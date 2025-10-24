<?php

namespace App\Filament\Resources;

use App\Exports\RimBrandExampleExport;
use App\Filament\Imports\RimBrandImporter;
use App\Filament\Resources\RimBrandResource\Pages;
use App\Filament\Resources\RimBrandResource\RelationManagers;
use App\Models\RimBrand;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maatwebsite\Excel\Facades\Excel;

class RimBrandResource extends BaseResource
{
    protected static ?string $model = RimBrand::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Rims';
    protected static ?int $navigationSort = 20;
    protected static ?string $navigationLabel = 'Rim Brands';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Brand Name')
                ->required(),

            SpatieMediaLibraryFileUpload::make('logo')
                ->label('Brand Logo')
                ->collection('logo')
                ->image()
                ->preserveFilenames()
                ->maxSize(2048),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->label('Brand Name')->searchable(),
                TextColumn::make('created_at')->dateTime()->label('Created At')->sortable(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Bulk Upload')
                    ->importer(RimBrandImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Rim Brands')
                    ->modalDescription('Are you sure you want to delete all rim brands? This action cannot be undone.')
                    ->action(fn () => RimBrand::query()->delete()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (RimBrand $record) {
                        \App\Models\Rim::where('rim_brand_id', $record->id)->update(['rim_brand_id' => null]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->before(function ($records) {
                        foreach ($records as $record) {
                            \App\Models\Rim::where('rim_brand_id', $record->id)->update(['rim_brand_id' => null]);
                        }
                    }),
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
            'index' => Pages\ListRimBrands::route('/'),
            'create' => Pages\CreateRimBrand::route('/create'),
            'edit' => Pages\EditRimBrand::route('/{record}/edit'),
        ];
    }
}
