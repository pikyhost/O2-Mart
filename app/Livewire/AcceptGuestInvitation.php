<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Models\Invitation;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Validation\Rules\Password;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class AcceptGuestInvitation extends SimplePage
{
    use InteractsWithFormActions, InteractsWithForms;

    protected static string $view = 'livewire.accept-guest-invitation';

    public int $invitation;

    private Invitation $invitationModel;

    public ?array $data = [];

    public function mount(): void
    {
        $this->invitationModel = Invitation::findOrFail($this->invitation);

        $this->form->fill([
            'email' => $this->invitationModel->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('password')
                    ->label(__('Password'))
                    ->revealable()
                    ->password()
                    ->required()
                    ->rule(Password::default()),

                TextInput::make('passwordConfirmation')
                    ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->dehydrated(false)
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $this->invitationModel = Invitation::findOrFail($this->invitation);

        $user = User::create([
            'name' => $this->invitationModel->name,
            'phone' => $this->invitationModel->phone,
            'preferred_language' => $this->invitationModel->preferred_language,
            'password' => bcrypt($this->form->getState()['password']),
            'email' => $this->invitationModel->email,
            'email_verified_at' => now(),
        ]);

        $user->assignRole(UserRole::Client->value);

        auth()->login($user);
        $this->invitationModel->delete();

        $this->redirect('/client');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('register')
                ->color('info')
                ->label(__('Register'))
                ->submit('create'),
        ];
    }

    public function getHeading(): string
    {
        return __('Accept Invitation');
    }

    public function hasLogo(): bool
    {
        return false;
    }

    public function getSubHeading(): string
    {
        return __('Create your account and join now!');
    }

    protected function getPreferredLanguageFormComponent()
    {
        return Radio::make('preferred_language')
            ->label(__('Preferred Language'))
            ->options([
                'en' => __('English'),
                'ar' => __('Arabic'),
            ])
            ->default(fn () => $this->getBrowserPreferredLanguage())
            ->columns(2)
            ->required();
    }

    /**
     * Get the browser's preferred language.
     */
    protected function getBrowserPreferredLanguage(): string
    {
        $preferredLanguages = request()->getPreferredLanguage(['en', 'ar']);
        return $preferredLanguages ?: 'en'; // Default to English if no match found
    }
}
