<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\Resources\FaqResource;
use App\Models\Faq;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListFaqs extends BaseListPage
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(function () {
                return Faq::count() < 1;
            }),
        ];
    }
}
