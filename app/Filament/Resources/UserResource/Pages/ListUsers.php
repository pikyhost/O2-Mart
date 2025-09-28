<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource;
use App\Mail\TeamInvitationMail;
use App\Models\Invitation;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('inviteUser')
                ->label(__('User Invitation'))
                ->color('gray')
                ->form([
                    TextInput::make('email')
                        ->label(__('Email address'))
                        ->unique(User::class, 'email')
                        ->email()
                        ->required()
                        ->validationMessages([
                            'unique' => __('email.unique'),
                        ]),
                    Select::make('role_id')
                    ->label(__('roles'))
                        ->helperText(__('roles.helper'))
                        ->options(
                            Role::pluck('name', 'id')->toArray()
                        )
                        ->default(Role::where('name', UserRole::Client->value)->value('id')) // Optional default
                        ->required(), // Optional, if not nullable
                ])
                ->action(function ($data) {
                    $invitation = Invitation::create([
                        'email' => $data['email'],
                        'role_id' => $data['role_id'],
                    ]);

                    Mail::to($invitation->email)->send(new TeamInvitationMail($invitation));

                    Notification::make('invitedSuccess')
                        ->body(__('notification.invited_success'))
                        ->success()
                        ->send();
                }),
        ];
    }

    public function setPage($page, $pageName = 'page'): void
    {
        parent::setPage($page, $pageName);

        $this->dispatch('scroll-to-top');
    }
}
