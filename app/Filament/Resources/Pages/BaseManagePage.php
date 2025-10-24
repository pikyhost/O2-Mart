<?php

namespace App\Filament\Resources\Pages;

use Filament\Resources\Pages\ManageRecords;

abstract class BaseManagePage extends ManageRecords
{
    /**
     * Get header widgets including the import progress widget
     * This will be automatically included on all manage pages that extend this class
     */
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\AutoPartResource\Widgets\ImportProgressWidget::class,
        ];
    }
}
