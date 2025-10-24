<?php

namespace App\Filament\Resources\AboutUsResource\Pages;

use App\Filament\Resources\AboutUsResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListAboutUs extends BaseListPage
{
    protected static string $resource = AboutUsResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
