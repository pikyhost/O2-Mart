<?php

namespace App\Filament\Resources;

use App\Exports\BatteryBrandExampleExport;
use App\Filament\Imports\BatteryBrandImporter;
use App\Models\BatteryBrand;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;
use App\Filament\Resources\BatteryBrandResource\Pages;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class BatteryBrandResource extends BaseResource
{
    protected static ?string $model = BatteryBrand::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Batteries';
    protected static ?string $navigationLabel = 'Battery Brands';
    protected static ?int $navigationSort = 13;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('value')
                ->required()
                ->maxLength(255),
                
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
                Tables\Columns\SpatieMediaLibraryImageColumn::make('logo')
                    ->label('Logo')
                    ->collection('logo')
                    ->circular()
                    ->height(40),
                    
                Tables\Columns\TextColumn::make('value')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Import Brands')
                    ->importer(BatteryBrandImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Battery Brands')
                    ->modalDescription('Are you sure you want to delete all battery brands? This action cannot be undone.')
                    ->action(fn () => BatteryBrand::query()->delete()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (BatteryBrand $record) {
                        \App\Models\Battery::where('battery_brand_id', $record->id)->update(['battery_brand_id' => null]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                \App\Models\Battery::where('battery_brand_id', $record->id)->update(['battery_brand_id' => null]);
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBatteryBrands::route('/'),
            'create' => Pages\CreateBatteryBrand::route('/create'),
            'edit' => Pages\EditBatteryBrand::route('/{record}/edit'),
        ];
    }
}
