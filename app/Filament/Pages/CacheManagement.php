<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;

class CacheManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';
    protected static string $view = 'filament.pages.cache-management';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $title = 'Cache Management';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('optimize_clear')
                ->label('Clear All Cache')
                ->color('danger')
                ->action(function () {
                    Artisan::call('optimize:clear');
                    Notification::make()
                        ->title('Cache Cleared')
                        ->success()
                        ->send();
                }),
            Action::make('optimize')
                ->label('Optimize & Cache')
                ->color('success')
                ->action(function () {
                    Artisan::call('optimize');
                    Notification::make()
                        ->title('System Optimized')
                        ->success()
                        ->send();
                }),
        ];
    }
}