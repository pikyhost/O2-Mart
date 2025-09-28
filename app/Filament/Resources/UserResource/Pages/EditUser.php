<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    public static function getNavigationLabel(): string
    {
        return __('Edit User');
    }

    protected function afterSave(): void
    {
        /** @var MustVerifyEmail $user */
        $user = $this->record;

        // Check if email was changed and user needs verification
        if ($user instanceof MustVerifyEmail &&
            $user->wasChanged('email') &&
            !$user->hasVerifiedEmail()) {

            // Mark email as unverified
            $user->email_verified_at = null;
            $user->saveQuietly(); // Save without triggering events

            // Send verification notification
            $notification = new VerifyEmail();
            $notification->url = Filament::getVerifyEmailUrl($user);

            $user->notify($notification);
        }
    }
}
