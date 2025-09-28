<?php

namespace App\Filament\Pages;


use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    protected function getHeaderWidgets(): array
    {
        return [
            AccountWidget::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
        ];
    }

    protected function shouldPersistTableFiltersInSession(): bool
    {
        return true;
    }
}
