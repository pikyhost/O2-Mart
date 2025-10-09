<?php

namespace App\Filament\Resources;

use App\Enums\CouponType;
use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->columnSpanFull()
                        ->required()
                        ->maxLength(255)
                        ->unique(Coupon::class, 'name', ignoreRecord: true),
                    Forms\Components\TextInput::make('code')
                        ->required()
                        ->maxLength(255)
                        ->unique(Coupon::class, 'code', ignoreRecord: true),
                    Forms\Components\Select::make('type')
                        ->live()
                        ->options(CouponType::class)
                        ->enum(CouponType::class)
                        ->required()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            if ($state === 'free_shipping') {
                                $set('value', null);
                            }
                        }),
                    Forms\Components\TextInput::make('value')
                        ->visible(fn(Forms\Get $get) => $get('type') == 'discount_amount' || $get('type') == 'discount_percentage')
                        ->numeric()
                        ->minValue(1)
                        ->nullable()
                        ->helperText(fn(Forms\Get $get) => match($get('type')) {
                            'discount_percentage' => 'Enter percentage (e.g., 10 for 10%)',
                            'discount_amount' => 'Enter fixed amount',
                            default => null
                        }),
                    Forms\Components\DateTimePicker::make('expires_at')
                        ->nullable(),
                    Forms\Components\TextInput::make('min_order_amount')
                        ->numeric()
                        ->minValue(1)
                        ->nullable(),

                    Forms\Components\TextInput::make('usage_limit')
                        ->required()
                        ->numeric(),

                    Forms\Components\TextInput::make('usage_limit_per_user')
                        ->required()
                        ->numeric(),

                    Forms\Components\Checkbox::make('is_active')
                        ->columnSpanFull()
                        ->default(true),
                ])->columns(2)
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->sortable(['created_at' => 'desc'])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn (string $state) => ucwords(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('value')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) : '-'),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_order_amount')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) : '-'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ], Tables\Enums\FiltersLayout::Modal)
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
