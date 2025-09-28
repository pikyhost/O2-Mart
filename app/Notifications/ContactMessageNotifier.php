<?php

// app/Notifications/ContactMessageNotifier.php

namespace App\Notifications;

use App\Models\ContactMessage;
use App\Models\Setting;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\App;

class ContactMessageNotifier
{
    public static function notify(ContactMessage $message): void
    {
        $adminUsers = User::role(['admin', 'super_admin'])->get();

        $isAdminSender = auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin']);

        foreach ($adminUsers as $admin) {
            App::setLocale($admin->locale ?? config('app.locale'));

            Notification::make()
                ->title(__('contact_message_notification.admin.title'))
                ->success()
                ->body(__('contact_message_notification.admin.body', ['name' => $message->name]))
                ->actions([
                    Action::make('view')
                        ->label(__('contact_message_notification.admin.view_button'))
                        ->url(route('filament.admin.resources.contact-messages.view', ['record' => $message->id]))
                        ->markAsRead(),
                ])
                ->sendToDatabase($admin);
        }

        if (auth()->check() && ! $isAdminSender) {
            App::setLocale(auth()->user()->locale ?? config('app.locale'));

            Notification::make()
                ->title(__('contact_message_notification.user.title'))
                ->success()
                ->body(__('contact_message_notification.user.body'))
                ->actions([
                    Action::make('view')
                        ->label(__('contact_message_notification.user.view_button'))
                        ->url(route('filament.client.resources.contact-messages.view', ['record' => $message->id]))
                        ->markAsRead(),
                ])
                ->sendToDatabase(auth()->user());
        }

        $adminEmail = Setting::first()?->email;

        if ($adminEmail) {
            \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)
                ->notify(new ContactMessageMailNotification($message));
        }
    }
}
