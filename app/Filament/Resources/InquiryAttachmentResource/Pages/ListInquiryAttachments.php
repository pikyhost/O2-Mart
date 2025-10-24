<?php

namespace App\Filament\Resources\InquiryAttachmentResource\Pages;

use App\Filament\Resources\InquiryAttachmentResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListInquiryAttachments extends BaseListPage
{
    protected static string $resource = InquiryAttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
