<?php

namespace App\Filament\Resources\MobileVanServiceResource\Pages;

use App\Filament\Resources\MobileVanServiceResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManageMobileVanServiceWorkingHour extends ManageRelatedRecords
{
    protected static string $resource = MobileVanServiceResource::class;

    protected static string $relationship = 'workingHours';

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function getNavigationLabel(): string
    {
        return 'Working Hours List';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('center_id')
            ->columns([
                TextColumn::make('day.name')
                    ->searchable()
                    ->badge(),
                TextColumn::make('opening_time'),
                TextColumn::make('closing_time'),
                TextColumn::make('is_closed')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'danger' : 'success')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Closed' : 'Open'),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
