<?php

namespace App\Filament\Resources\CouponUsageResource\Pages;

use App\Filament\Resources\CouponUsageResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListCouponUsages extends BaseListPage
{
    protected static string $resource = CouponUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
