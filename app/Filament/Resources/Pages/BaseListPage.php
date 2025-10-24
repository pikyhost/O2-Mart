<?php

namespace App\Filament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;

abstract class BaseListPage extends ListRecords
{
    /**
     * Get header widgets including the import progress widget
     * This will be automatically included on all list pages that extend this class
     */
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\AutoPartResource\Widgets\ImportProgressWidget::class,
        ];
    }
}
