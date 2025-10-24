<?php

namespace App\Filament\Resources\ContactUsResource\Pages;

use App\Filament\Resources\ContactUsResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListContactUs extends BaseListPage
{
    protected static string $resource = ContactUsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
