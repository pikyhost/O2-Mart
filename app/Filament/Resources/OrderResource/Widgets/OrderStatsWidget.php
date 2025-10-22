<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalOrders = Order::count();
        $openOrders = Order::whereIn('status', ['pending', 'processing', 'shipped'])->count();
        $completedOrders = Order::whereIn('status', ['completed', 'delivered'])->count();
        $averageOrderValue = Order::avg('total') ?? 0;
        $todayOrders = Order::whereDate('created_at', today())->count();
        $monthlyRevenue = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('total');

        return [
            Stat::make('Total Orders', number_format($totalOrders))
                ->description('All time orders')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary')
                ->chart([7, 12, 18, 25, 32, 38, $totalOrders]),
                
            Stat::make('Open Orders', number_format($openOrders))
                ->description('Pending, Processing & Shipped')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([5, 8, 12, 15, 18, 22, $openOrders]),
                
            Stat::make('Completed Orders', number_format($completedOrders))
                ->description('Successfully delivered')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
                
            Stat::make('Average Order Value', number_format($averageOrderValue, 2) . ' AED')
                ->description('Average per order')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),
                
            Stat::make('Today\'s Orders', number_format($todayOrders))
                ->description('Orders placed today')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
                
            Stat::make('Monthly Revenue', number_format($monthlyRevenue, 2) . ' AED')
                ->description('This month')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),
        ];
    }
}
