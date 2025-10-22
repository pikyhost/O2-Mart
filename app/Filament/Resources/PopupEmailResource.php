<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PopupEmailResource\Pages;
use App\Models\PopupEmail;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;

class PopupEmailResource extends BaseResource
{
    protected static ?string $model = PopupEmail::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationGroup = 'Content Management';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('popup_id')
                    ->label('Popup')
                    ->relationship('popup', 'title')
                    ->searchable()
                    ->nullable(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('popup.title')
                    ->label('Popup')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('popup.coupon.code')
                    ->label('Coupon Won')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ?? '-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('popup_id')
                    ->label('Popup')
                    ->relationship('popup', 'title'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPopupEmails::route('/'),
        ];
    }
}