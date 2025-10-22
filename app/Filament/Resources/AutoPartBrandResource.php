<?php

namespace App\Filament\Resources;

use App\Filament\Imports\AutoPartBrandImporter;
use App\Exports\AutoPartBrandExporter;
use App\Filament\Resources\AutoPartBrandResource\Pages;
use App\Models\AutoPartBrand;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AutoPartBrandResource extends BaseResource
{
    protected static ?string $model = AutoPartBrand::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Auto Parts';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationLabel = 'AutoPart Brands';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label('Brand Name')->required()->unique(ignoreRecord: true),
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
                TextColumn::make('name')->label('Brand')->searchable()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (AutoPartBrand $record) {
                        try {
                            \App\Models\AutoPart::where('auto_part_brand_id', $record->id)->update(['auto_part_brand_id' => null]);
                        } catch (\Exception $e) {
                            \Log::error('Error updating auto parts before brand deletion: ' . $e->getMessage());
                        }
                    }),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(AutoPartBrandImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All AutoPart Brands')
                    ->modalDescription('Are you sure you want to delete all autopart brands? This action cannot be undone.')
                    ->action(fn () => AutoPartBrand::query()->delete()),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->before(function ($records) {
                        foreach ($records as $record) {
                            try {
                                \App\Models\AutoPart::where('auto_part_brand_id', $record->id)->update(['auto_part_brand_id' => null]);
                            } catch (\Exception $e) {
                                \Log::error('Error updating auto parts before bulk brand deletion: ' . $e->getMessage());
                            }
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAutoPartBrands::route('/'),
            'create' => Pages\CreateAutoPartBrand::route('/create'),
            'edit' => Pages\EditAutoPartBrand::route('/{record}/edit'),
        ];
    }
}
