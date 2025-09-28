<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VinRecordResource\Pages;
use App\Filament\Resources\VinRecordResource\RelationManagers;
use App\Models\VinRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VinRecordResource extends Resource
{
    protected static ?string $model = VinRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string {return 'Vehicle Data';}
    public static function getNavigationSort(): ?int  { return 30;  }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('vin')
                ->label('VIN Number')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(17),

            Forms\Components\Select::make('car_model_id')
                ->label('Car Model')
                ->relationship('carModel', 'name')
                ->searchable()
                ->required(),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vin')
                    ->label('VIN')
                    ->searchable(),

                Tables\Columns\TextColumn::make('carModel.name')
                    ->label('Car Model')
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListVinRecords::route('/'),
            'create' => Pages\CreateVinRecord::route('/create'),
            'edit' => Pages\EditVinRecord::route('/{record}/edit'),
        ];
    }
}
