<?php

namespace App\Filament\Resources\InquiryAttachmentResource\Pages;

use App\Filament\Resources\InquiryAttachmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInquiryAttachments extends ListRecords
{
    protected static string $resource = InquiryAttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
