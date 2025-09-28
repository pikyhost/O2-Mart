<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Models\Invitation;
use App\Models\User;
use App\Rules\CustomPassword;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class AcceptInvitation extends SimplePage
{
    use InteractsWithFormActions, InteractsWithForms;

    protected static string $view = 'livewire.accept-invitation';

    public int $invitation;

    private Invitation $invitationModel;

    public ?array $data = [];

    public function mount(): void
    {
        $this->invitationModel = Invitation::findOrFail($this->invitation);

        $roleIds = $this->invitationModel->roles ?? [];

        $roles = \Spatie\Permission\Models\Role::whereIn('id', $roleIds)->pluck('name')->toArray();

        if (! in_array(UserRole::Client->value, $roles)) {
            $roles[] = __('client');
        }

        $roleNames = $this->formatRoles($roles);

        $this->form->fill([
            'email' => $this->invitationModel->email,
            'role' => $roleNames,
        ]);
    }

    private function formatRoles(array $roles): string
    {
        if (count($roles) === 1) {
            return $roles[0];
        }

        return implode(', ', $roles);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('filament-panels::pages/auth/register.form.name.label'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
//                TextInput::make('role')
//                    ->label('Roles')
//                    ->formatStateUsing(fn ($state) => Str::headline($state))
//                    ->disabled(),
                TextInput::make('email')
                    ->label(__('filament-panels::pages/auth/register.form.email.label'))
                    ->disabled(),
                $this->getPhoneFormComponent(),
                TextInput::make('password')
                    ->revealable()
                    ->label(__('filament-panels::pages/auth/register.form.password.label'))
                    ->password()
                    ->required()
                    ->rule(['min:8', new CustomPassword()])
                    ->same('passwordConfirmation')
                    ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute')),
                TextInput::make('passwordConfirmation')
                    ->revealable()
                    ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
                    ->password()
                    ->required()
                    ->dehydrated(false),

                $this->getPreferredLanguageFormComponent(),
                $this->getTermsAndConditionsComponent(),
            ])
            ->statePath('data');
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

    protected function getTermsAndConditionsComponent()
    {
        // Get the current locale (ar or en)
        $locale = App::getLocale();

        // Define URLs for Terms & Privacy Policy pages
        $termsUrl = $locale === 'ar' ? '/ar/terms-and-conditions' : '/terms-and-conditions';
        $privacyUrl = $locale === 'ar' ? '/ar/privacy-and-policy' : '/privacy-and-policy';

        // Get the translated string
        $translatedString = __('custom.terms_and_conditions');

        // Replace placeholders {terms} and {privacy} with actual links
        $translatedString = Str::replace(
            ['{terms}', '{privacy}'],
            [
                "<a href=\"$termsUrl\" class=\"text-blue-600 underline hover:text-blue-700 font-medium transition-colors duration-200 ease-in-out\" target=\"_blank\">" . __('custom.terms_link') . "</a>",
                "<a href=\"$privacyUrl\" class=\"text-blue-600 underline hover:text-blue-700 font-medium transition-colors duration-200 ease-in-out\" target=\"_blank\">" . __('custom.privacy_link') . "</a>"
            ],
            $translatedString
        );

        return Checkbox::make('accept')
            ->label(fn () => new HtmlString($translatedString))
            ->required()
            ->dehydrated(false);
    }

    protected function getPhoneFormComponent(): Component
    {
        return PhoneInput::make('phone')
            ->enableIpLookup(true) // Enable IP-based country detection
            ->initialCountry(fn () => geoip(request()->ip())['country_code2'] ?? 'US')
            ->required()
            ->rules([
                'max:20', // Match database column limit
                'unique:users,phone', // Ensure uniqueness in the `users` table
            ])
            ->label(__('profile.phone'))
            ->columnSpanFull();
    }

    public function create(): void
    {
        $this->invitationModel = Invitation::find($this->invitation);

        $user = User::create([
            'name' => $this->form->getState()['name'],
            'password' => bcrypt($this->form->getState()['password']),
            'email' => $this->invitationModel->email,
            'email_verified_at' => now()
        ]);

        $roleIds = $this->invitationModel->roles ?? [];

        $roles = \Spatie\Permission\Models\Role::whereIn('id', $roleIds)->pluck('name')->toArray();

        if (! in_array(UserRole::Client->value, $roles)) {
            $roles[] = UserRole::Client->value;
        }

        foreach ($roles as $role) {
            if (\Spatie\Permission\Models\Role::findByName($role, 'web')) {
                $user->assignRole($role);
            } else {
                logger("Role '{$role}' does not exist.");
            }
        }

        auth()->login($user);
        $this->invitationModel->delete();

        $this->redirect('/client');
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('register')
                ->color('info')
                ->label(__('filament-panels::pages/auth/register.form.actions.register.label'))
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
}
