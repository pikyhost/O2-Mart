<?php

namespace App\Traits;

use App\Actions\MakeCostZeroBulkAction;
use Filament\Tables\Actions\BulkAction;

trait HasMakeCostZeroAction
{
    public static function makeCostZeroBulkAction(): BulkAction
    {
        return BulkAction::make('makeCostZero')
            ->label(__('Set Cost to 0'))
            ->icon('heroicon-o-currency-dollar')
            ->requiresConfirmation()
            ->action(fn ($records) => (new MakeCostZeroBulkAction())->execute($records))
            ->deselectRecordsAfterCompletion();
    }
}
