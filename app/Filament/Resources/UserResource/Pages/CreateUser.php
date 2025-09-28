<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function afterCreate(): void
    {
        /** @var MustVerifyEmail $user */
        $user = $this->record;

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            $notification = new VerifyEmail();
            $notification->url = Filament::getVerifyEmailUrl($user);

            $user->notify($notification);
        }
    }
}
