<?php

namespace App\Filament\Resources\CouponUsageResource\Pages;

use App\Filament\Resources\CouponUsageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCouponUsages extends ListRecords
{
    protected static string $resource = CouponUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
