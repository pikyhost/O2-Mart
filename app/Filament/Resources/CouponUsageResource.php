<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponUsageResource\Pages;
use App\Models\CouponUsage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponUsageResource extends Resource
{
    protected static ?string $model = CouponUsage::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $modelLabel = 'Coupon Usage';

    protected static ?string $navigationLabel = 'Coupon Usages';

    public static function getModelLabel(): string
    {
        return __('Coupon Usages');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Coupon Usages');
    }

    public static function getNavigationLabel(): string
    {
        return __('Coupon Usages');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Coupon Usages');
    }

    public static function getLabel(): ?string
    {
        return __('Coupon Usage');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Usage Details')
                    ->schema([
                        Forms\Components\Select::make('coupon_id')
                            ->label(__('Coupon'))
                            ->relationship('coupon', 'code')
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('user_id')
                            ->label(__('Customer'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('order_id')
                            ->label(__('Order'))
                            ->relationship('order', 'id')
                            ->searchable()
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('coupon.code')
                    ->label(__('Coupon Code'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label(__('Customer'))
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        // If user_id is set and user exists, return the user's name
                        if ($record->user) {
                            return $record->user->name;
                        }

                        // For guest users, try to fetch guest name from order
                        if ($record->order) {
                            return $record->order->contact_name ?? 'Guest';
                        }

                        // Fallback for guests
                        return 'Guest';
                    }),

                TextColumn::make('email')
                    ->label(__('Email'))
                    ->getStateUsing(function ($record) {
                        // If user_id is set and user exists, return the user's email
                        if ($record->user) {
                            return $record->user->email;
                        }

                        // For guest users, try to fetch email from order
                        if ($record->order) {
                            return $record->order->contact_email ?? '-';
                        }

                        // Fallback
                        return '-';
                    }),

                Tables\Columns\TextColumn::make('order.id')
                    ->label(__('Order ID'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? "#$state" : '-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Used At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCouponUsages::route('/'),
        ];
    }
}
