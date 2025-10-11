<?php
namespace App\Filament\Resources;

use App\Filament\Exports\TyreAttributeExporter;
use App\Filament\Imports\TyreAttributeImporter;
use App\Filament\Resources\TyreAttributeResource\Pages;
use App\Models\TyreAttribute;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;

use Filament\Tables\Table;

class TyreAttributeResource extends Resource
{
    protected static ?string $model = TyreAttribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Tyres';
    protected static ?int $navigationSort = 28;
    protected static ?string $navigationLabel = 'Tyres Attribute';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
            Select::make('car_make_id')
                ->label('Car Make')
                ->relationship('make', 'name')
                ->reactive()
                ->required(),

            Select::make('car_model_id')
                ->label('Car Model')
                ->options(fn (Get $get) =>
                    \App\Models\CarModel::where('car_make_id', $get('car_make_id'))->pluck('name', 'id')
                )
                ->reactive()
                ->required(),

            Select::make('model_year')
                ->label('Model Year')
                ->options(function (Get $get, $record) {
                    $model = \App\Models\CarModel::find($get('car_model_id'));
                    if (!$model && !$record) return [];
                    
                    // If editing, include current year even if outside model range
                    $years = [];
                    if ($model) {
                        $years = array_combine(
                            range($model->year_from, $model->year_to),
                            range($model->year_from, $model->year_to)
                        );
                    }
                    
                    // Add current record year if not in range
                    if ($record && $record->model_year && !isset($years[$record->model_year])) {
                        $years[$record->model_year] = $record->model_year;
                        ksort($years);
                    }
                    
                    return $years;
                })
                ->required(),

                ]),

                Grid::make(2)->schema([
                    TextInput::make('trim')
                        ->label('Trim')
                        ->afterStateHydrated(function (TextInput $component, $record) {
                            if ($record) {
                                $component->state($record->trim);
                            }
                        }),

                    TextInput::make('tyre_attribute')
                        ->label('Tyre Attribute'),
                    TextInput::make('rare_attribute')
                        ->label('Rare Attribute')
                        ->nullable()
                        ->afterStateHydrated(function (TextInput $component, $record) {
                            if ($record) {
                                $component->state($record->rare_attribute);
                            }
                        }),
    
                    Checkbox::make('tyre_oem')
                        ->label('Tyre OEM')
                        ->default(false),

                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('make.name')->label('Car Make'),
                Tables\Columns\TextColumn::make('model.name')->label('Car Model'),
                Tables\Columns\TextColumn::make('model_year')->label('Model Year'),
                Tables\Columns\TextColumn::make('trim')->label('Trim'),
                Tables\Columns\TextColumn::make('tyre_attribute')->label('Tyre Attribute'),
                Tables\Columns\TextColumn::make('rare_attribute')->label('Rare Attribute'),
                Tables\Columns\IconColumn::make('tyre_oem')->label('Tyre OEM')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Import Attributes')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('danger')
                    ->importer(TyreAttributeImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Tyre Attributes')
                    ->modalDescription('Are you sure you want to delete all tyre attributes? This action cannot be undone.')
                    ->action(fn () => TyreAttribute::query()->delete()),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTyreAttributes::route('/'),
            'create' => Pages\CreateTyreAttribute::route('/create'),
            'edit' => Pages\EditTyreAttribute::route('/{record}/edit'),
        ];
    }
}
