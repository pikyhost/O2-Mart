<?php

namespace App\Filament\Resources\PolicyResource\Pages;

use App\Filament\Resources\PolicyResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListPolicies extends BaseListPage
{
    protected static string $resource = PolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
