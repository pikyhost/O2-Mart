<?php

namespace App\Filament\Resources\NewsletterSubscriberResource\Pages;

use App\Filament\Resources\NewsletterSubscriberResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNewsletterSubscribers extends ManageRecords
{
    protected static string $resource = NewsletterSubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make() ->mutateFormDataUsing(function (array $data): array {
                $data['ip_address'] = request()->ip();

                return $data;
            }),
        ];
    }
}
