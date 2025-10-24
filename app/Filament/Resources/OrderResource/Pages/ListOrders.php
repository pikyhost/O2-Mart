<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Filament\Resources\Pages\BaseListPage;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Pages\Actions\CreateAction;

class ListOrders extends BaseListPage
{
    protected static string $resource = OrderResource::class;
    
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\AutoPartResource\Widgets\ImportProgressWidget::class,
            OrderResource\Widgets\OrderStatsWidget::class,
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(Order::where('status', 'pending')->count())
                ->badgeColor('warning'),
            
            'processing' => Tab::make('Processing')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'processing'))
                ->badge(Order::where('status', 'processing')->count())
                ->badgeColor('info'),
            
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed'))
                ->badge(Order::where('status', 'completed')->count())
                ->badgeColor('success'),
            
            'shipped' => Tab::make('Shipped')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'shipped'))
                ->badge(Order::where('status', 'shipped')->count())
                ->badgeColor('primary'),
            
            'delivered' => Tab::make('Delivered')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'delivered'))
                ->badge(Order::where('status', 'delivered')->count())
                ->badgeColor('success'),
            
            'cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled'))
                ->badge(Order::where('status', 'cancelled')->count())
                ->badgeColor('danger'),
            
            'payment_failed' => Tab::make('Payment Failed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'payment_failed'))
                ->badge(Order::where('status', 'payment_failed')->count())
                ->badgeColor('danger'),
        ];
    }
}
