<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Resources\OrderResource\Pages\ViewOrder;
use Filament\Resources\Resource;
use Filament\Pages\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;


use Filament\Resources\Form;
use Filament\Infolists\Infolist;
use App\Models\Order;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'Orders';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'view' => ViewOrder::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'user',
            'addresses.area',
            'addresses.city',
            'items.buyable',
            'items.mobileVan',
            'items.installationCenter',
            'coupon',
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make('Order Details')->tabs([
                Tab::make('Customer Info')->schema([
                    Section::make()->schema([
                        TextEntry::make('user.name')->label('User Name')->hidden(fn ($record) => is_null($record->user_id)),
                        TextEntry::make('user.email')->label('User Email')->hidden(fn ($record) => is_null($record->user_id)),
                        TextEntry::make('user.phone')->label('User Phone')->hidden(fn ($record) => is_null($record->user_id)),
                        TextEntry::make('contact_name')->label('Guest Name')->hidden(fn ($record) => !is_null($record->user_id)),
                        TextEntry::make('contact_email')->label('Guest Email')->hidden(fn ($record) => !is_null($record->user_id)),
                        TextEntry::make('contact_phone')->label('Guest Phone')->hidden(fn ($record) => !is_null($record->user_id)),
                    ])->columns(3),
                ]),

                Tab::make('Vehicle')->schema([
                    Section::make()->schema([
                        TextEntry::make('car_make')->label('Car Make')->default('-'),
                        TextEntry::make('car_model')->label('Car Model')->default('-'),
                        TextEntry::make('car_year')->label('Car Year')->default('-'),
                        TextEntry::make('plate_number')->label('Plate Number')->default('-'),
                        TextEntry::make('vin')->label('VIN')->default('-'),
                    ])->columns(3),
                ]),

                Tab::make('Shipment Tracking')->schema([
                    Section::make()->schema([
                        ViewEntry::make('tracking_timeline')
                            ->label('Tracking Timeline')
                            ->view('filament.tracking-timeline', [
                                'order' => fn ($record = null) => $record,
                            ])
                            ->visible(fn ($record) => filled($record->tracking_number)),
                    ]),
                ]),

                Tab::make('Shipping Address')->schema([
                    Section::make()->schema([
                        TextEntry::make('city')
                            ->label('City')
                            ->getStateUsing(fn ($record) => $record->addresses()->where('type', 'shipping')->first()?->city?->name ?? '-'),
                        TextEntry::make('area')
                            ->label('Area')
                            ->getStateUsing(fn ($record) => $record->addresses()->where('type', 'shipping')->first()?->area?->name ?? '-'),
                        TextEntry::make('address')
                            ->label('Street Address')
                            ->getStateUsing(fn ($record) => $record->addresses()->where('type', 'shipping')->first()?->address_line ?? '-'),
                        TextEntry::make('phone')
                            ->label('Phone')
                            ->getStateUsing(fn ($record) => $record->addresses()->where('type', 'shipping')->first()?->phone ?? '-'),
                        TextEntry::make('notes')
                            ->label('Notes')
                            ->getStateUsing(fn ($record) => $record->addresses()->where('type', 'shipping')->first()?->notes ?? '-'),
                    ])->columns(2),
                ]),

                Tab::make('Order Items')->schema([
                    Section::make()->schema([
                        ViewEntry::make('items')
                            ->view('filament.order-items-table', [
                                'order' => fn ($record = null) => $record,
                            ]),
                    ]),
                ]),
                Tab::make('Shipping Cost')->schema([
                    Section::make('Shipping Cost')->schema([
                        TextEntry::make('shipping_cost')->label('Total (Calculator + Area)')
                            ->formatStateUsing(fn ($state) => number_format((float)$state, 2) . ' AED')
                            ->color('primary'),
                        TextEntry::make('area_cost')
                            ->label('Area Cost Alone')
                            ->getStateUsing(fn ($record) => $record->addresses()->where('type', 'shipping')->first()?->area?->shipping_cost ?? 0)
                            ->formatStateUsing(fn ($state) => $state ? number_format((float)$state, 2) . ' AED' : '0.00 AED')
                            ->color('warning'),
                        TextEntry::make('id')
                            ->label('Calculator Alone (Without Area)')
                            ->formatStateUsing(function ($record) {
                                $totalShipping = (float)($record->shipping_cost ?? 0);
                                $areaCost = (float)($record->addresses()->where('type', 'shipping')->first()?->area?->shipping_cost ?? 0);
                                $calculatorAlone = $totalShipping - $areaCost;
                                return number_format(max(0, $calculatorAlone), 2) . ' AED';
                            })
                            ->color('success'),
                    ])->columns(3),
                ]),

                Tab::make('Payment Info')->schema([
                    Section::make()->schema([
                        TextEntry::make('status')->label('Payment Status')
                            ->badge()
                            ->colors([
                                'completed' => 'success',
                                'pending' => 'warning',
                                'payment_failed' => 'danger',
                            ]),
                        TextEntry::make('payment_method')->label('Payment Method'),
                    ])->columns(2),
                ]),

                Tab::make('Shipping Status')->schema([
                    Section::make()->schema([
                        TextEntry::make('shipping_company')->label('Shipping Company'),
                    ])->columns(2),
                ]),

                Tab::make('Cost Breakdown')->schema([
                    Section::make('Coupon Information')->schema([
                        TextEntry::make('coupon.code')->label('Coupon Code')
                            ->badge()
                            ->color('success')
                            ->visible(fn ($record) => $record->coupon_id),
                        TextEntry::make('coupon.name')->label('Coupon Name')
                            ->visible(fn ($record) => $record->coupon_id),
                    ])->columns(2)
                    ->visible(fn ($record) => $record->coupon_id),
                    
                    Section::make('Cost Details')->schema([
                        TextEntry::make('subtotal')->label('Subtotal (AED)')
                            ->formatStateUsing(fn ($state) => number_format((float)$state, 2) . ' AED'),
                        TextEntry::make('shipping_cost')->label('Shipping (AED)')
                            ->formatStateUsing(fn ($state) => number_format((float)$state, 2) . ' AED'),
                        TextEntry::make('installation_fees')->label('Delivery and Installation (AED)')
                            ->formatStateUsing(fn ($state) => number_format((float)$state, 2) . ' AED')
                            ->default(0),
                        TextEntry::make('tax_amount')->label('VAT (AED)')
                            ->formatStateUsing(fn ($state) => number_format((float)$state, 2) . ' AED'),
                        TextEntry::make('discount')->label('Discount (AED)')->default(0)
                            ->formatStateUsing(fn ($state) => number_format((float)$state, 2) . ' AED')
                            ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
                        TextEntry::make('total')->label('Total (AED)')->color('primary')
                            ->formatStateUsing(fn ($state) => number_format((float)$state, 2) . ' AED'),
                    ])->columns(2),
                ]),

                Tab::make('Order Meta')->schema([
                    Section::make()->schema([
                        TextEntry::make('created_at')->label('Created At')->dateTime(),
                        TextEntry::make('tracking_number')->label('Tracking Number'),
                        TextEntry::make('tracking_url')
                            ->label('Tracking URL')
                            ->url(fn ($record = null) => $record?->tracking_url, true)
                            ->openUrlInNewTab(),
                    ])->columns(2),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('contact_name')
                    ->label('Customer')
                    ->getStateUsing(fn ($record) => $record->user?->name ?? $record->contact_name ?? '-')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'completed' => 'heroicon-o-check-circle',
                        'processing' => 'heroicon-o-arrow-path',
                        'payment_failed' => 'heroicon-o-x-circle',
                        'cancelled' => 'heroicon-o-x-mark',
                        'shipped' => 'heroicon-o-truck',
                        'delivered' => 'heroicon-o-check-badge',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'delivered' => 'success',
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'primary',
                        'payment_failed' => 'danger',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->formatStateUsing(fn ($state) => ucfirst($state ?? '-'))
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                    
                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 2) . ' AED')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                    
                Tables\Columns\TextColumn::make('shipping_cost')
                    ->label('Shipping')
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 2) . ' AED')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                    
                Tables\Columns\TextColumn::make('tax_amount')
                    ->label('VAT')
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 2) . ' AED')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                    
                Tables\Columns\TextColumn::make('discount')
                    ->label('Discount')
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 2) . ' AED')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                    
                Tables\Columns\TextColumn::make('total')
                    ->label('Total (AED)')
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 2) . ' AED')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('Tracking #')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                    
                Tables\Columns\TextColumn::make('shipping_company')
                    ->label('Shipping Company')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        'payment_failed' => 'Payment Failed',
                    ])
                    ->label('Status'),
                    
                SelectFilter::make('payment_method')
                    ->options([
                        'paymob' => 'Paymob',
                        'cod' => 'Cash on Delivery',
                    ])
                    ->label('Payment Method'),
                    
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Order From'),
                        DatePicker::make('created_until')
                            ->label('Order Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                    
                Filter::make('has_coupon')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('coupon_id'))
                    ->label('Has Coupon'),
                    
                Filter::make('high_value')
                    ->query(fn (Builder $query): Builder => $query->where('total', '>=', 1000))
                    ->label('High Value (≥1000 AED)'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Delete Order')
                    ->modalSubheading('Are you sure you want to delete this order?')
                    ->successNotificationTitle('Order deleted successfully.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
