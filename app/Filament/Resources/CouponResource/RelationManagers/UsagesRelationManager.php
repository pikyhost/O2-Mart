<?php

namespace App\Filament\Resources\CouponResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UsagesRelationManager extends RelationManager
{
    protected static string $relationship = 'usages';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label(__('Customer'))
                    ->relationship('user', 'name')
                    ->required(),

                Forms\Components\Select::make('order_id')
                    ->label(__('Order'))
                    ->relationship('order', 'id')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('Customer')),

                Tables\Columns\TextColumn::make('order.id')
                    ->label(__('Order ID'))
                    ->url(fn ($record) => OrderResource::getUrl('edit', ['record' => $record->order_id])),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Used At'))
                    ->dateTime(),
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
