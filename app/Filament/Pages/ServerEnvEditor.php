<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\File;
use Filament\Notifications\Notification;

class ServerEnvEditor extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $title = 'Server Settings';
    protected static string $view = 'filament.pages.env-editor';
    protected static ?string $slug = 'edit-server-settings';

    public ?string $APP_NAME;
    public ?string $APP_LOCALE = null;
    public ?string $MAIL_MAILER = null;
    public ?string $MAIL_HOST = null;
    public ?string $MAIL_PORT = null;
    public ?string $MAIL_USERNAME = null;
    public ?string $MAIL_PASSWORD = null;
    public ?string $MAIL_ENCRYPTION = null;
    public ?string $MAIL_FROM_ADDRESS = null;
    public ?string $MAIL_FROM_NAME = null;
    public ?string $GOOGLE_CLIENT_ID = null;
    public ?string $GOOGLE_CLIENT_SECRET = null;
    public ?string $GOOGLE_REDIRECT_URI = null;
    public ?string $PAYMOB_BASE_URL = null;
    public ?string $PAYMOB_API_KEY = null;
    public ?string $PAYMOB_IFRAME_ID = null;
    public ?string $PAYMOB_INTEGRATION_ID = null;
    public ?string $PAYMOB_PUBLIC_KEY = null;
    public ?string $PAYMOB_NOTIFICATION_URL = null;
    public ?string $PAYMOB_REDIRECTION_URL = null;
    public ?string $PAYMOB_FRONTEND_REDIRECT_URL = null;
    public ?string $PAYMOB_ENV = null;
    public ?string $JEEBLY_BASE_URL = null;
    public ?string $JEEBLY_USERNAME = null;
    public ?string $JEEBLY_PASSWORD = null;
    public ?string $JEEBLY_API_KEY = null;
    public ?string $JEEBLY_CLIENT_KEY = null;

    public function mount(): void
    {
        foreach ($this->getEditableKeys() as $key) {
            $this->{$key} = env($key) ?? null;
        }
    }


    public static function getNavigationGroup(): ?string
    {
        return 'Settings Management';
    }

    public function getHeading(): string|Htmlable
    {
        return __('Server Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Server Settings');
    }

    public static function getLabel(): ?string
    {
        return __('Server Settings');
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make(__('General Settings'))
                ->schema([
                    Forms\Components\TextInput::make('APP_NAME')
                        ->label(__('Application Name'))
                        ->helperText(__('The public name displayed across your application.'))
                        ->afterStateUpdated(function ($state, callable $set) {
                            $value = str_contains($state, ' ') ? "\"{$state}\"" : $state;

                            // Set the formatted value
                            $set('APP_NAME', $value);

                            // Remove quotes when saving to DB
                            $cleanValue = str_replace('"', '', $state);

                            // Update site_name in settings and clear/cache
                            \App\Models\Setting::updateSettings(['site_name' => $cleanValue]);
                        }),
                ]),

            Forms\Components\Section::make(__('Mail Settings'))
                ->schema([
                    TextInput::make('MAIL_MAILER')
                        ->label('Mailer Driver')
                        ->helperText('The mailer driver to use (e.g., smtp).'),

                    TextInput::make('MAIL_HOST')
                        ->label('Mail Host')
                        ->helperText('The hostname of your SMTP server.'),

                    TextInput::make('MAIL_PORT')
                        ->label('Mail Port')
                        ->numeric()
                        ->helperText('Port number used to connect to the mail server (e.g., 587).'),

                    TextInput::make('MAIL_USERNAME')
                        ->label('Mail Username')
                        ->helperText('Username used for SMTP authentication.'),

                    TextInput::make('MAIL_PASSWORD')
                        ->password()
                        ->revealable()
                        ->label('Mail Password')
                        ->helperText('Password used to authenticate your mail account.'),

                    TextInput::make('MAIL_ENCRYPTION')
                        ->label('Encryption Method')
                        ->helperText('Security protocol (e.g., tls or ssl) used for mail sending.'),

                    TextInput::make('MAIL_FROM_ADDRESS')
                        ->label('From Address')
                        ->helperText('Email address that will appear as the sender.'),

                    TextInput::make('MAIL_FROM_NAME')
                        ->label('From Name')
                        ->helperText('Name that appears on outgoing emails.'),
                ]),
            Forms\Components\Section::make(__('Google Login Settings'))
                ->schema([
                    TextInput::make('GOOGLE_CLIENT_ID')
                        ->label('Google Client ID')
                        ->required()
                        ->helperText('Enter the Client ID from your Google Console'),

                    TextInput::make('GOOGLE_CLIENT_SECRET')
                        ->label('Google Client Secret')
                        ->password()
                        ->revealable()
                        ->required()
                        ->helperText('Enter the Client Secret from your Google Console'),

                    TextInput::make('GOOGLE_REDIRECT_URI')
                        ->label('Redirect URI')
                        ->required()
                        ->helperText('e.g., http://localhost:8000/api/auth/google/callback'),
                ]),

            Forms\Components\Section::make('Paymob Settings')
                ->schema([
                    Forms\Components\TextInput::make('PAYMOB_BASE_URL')
                        ->label('Paymob Base URL')
                        ->required(),

                    Forms\Components\TextInput::make('PAYMOB_API_KEY')
                        ->label('Paymob API Key')
                        ->required(),

                    Forms\Components\TextInput::make('PAYMOB_IFRAME_ID')
                        ->label('Paymob Iframe ID')
                        ->required(),

                    Forms\Components\TextInput::make('PAYMOB_INTEGRATION_ID')
                        ->label('Paymob Integration ID')
                        ->required(),
                    Forms\Components\TextInput::make('PAYMOB_PUBLIC_KEY')
                            ->label('Paymob Public Key'),

                    Forms\Components\TextInput::make('PAYMOB_NOTIFICATION_URL')
                        ->label('Notification Callback URL'),

                    Forms\Components\TextInput::make('PAYMOB_REDIRECTION_URL')
                        ->label('Redirection URL'),

                    Forms\Components\TextInput::make('PAYMOB_FRONTEND_REDIRECT_URL')
                        ->label('Frontend Redirect URL')
                        ->helperText('URL that user is redirected to after successful/failed payment (e.g., http://localhost:3000)')
                        ->required(),

                    Forms\Components\TextInput::make('PAYMOB_ENV')
                        ->label('Environment (test/live)')
                        ->default('test'),
                ]),
            Forms\Components\Section::make('Jeebly Settings')
                ->schema([
                    Forms\Components\TextInput::make('JEEBLY_BASE_URL')
                        ->label('Jeebly Base URL')
                        ->required()
                        ->default('https://demo.jeebly.com/api'),

                    Forms\Components\TextInput::make('JEEBLY_USERNAME')
                        ->label('Jeebly Username')
                        ->required(),

                    Forms\Components\TextInput::make('JEEBLY_PASSWORD')
                        ->label('Jeebly Password')
                        ->password()
                        ->revealable()
                        ->required(),

                    Forms\Components\TextInput::make('JEEBLY_API_KEY')
                        ->label('Jeebly API Key')
                        ->required(),

                    Forms\Components\TextInput::make('JEEBLY_CLIENT_KEY')
                        ->label('Jeebly Client Key')
                        ->required(),
                ]),


        ];
    }

    public function save(): void
    {
        try {
            $envPath = base_path('.env');
            $envContent = File::get($envPath);

            foreach ($this->getEditableKeys() as $key) {
                $value = $this->{$key};
                $escapedValue = strpos($value, ' ') !== false || str_contains($value, ';') || str_contains($value, '"')
                    ? '"' . addslashes($value) . '"'
                    : $value;

                // replace line if key exists
                if (preg_match("/^{$key}=.*/m", $envContent)) {
                    $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$escapedValue}", $envContent);
                } else {
                    // add to end if not exists
                    $envContent .= "\n{$key}={$escapedValue}";
                }
            }

            File::put($envPath, $envContent);

            \Artisan::call('config:clear');

            Notification::make()
                ->title('Updated .env successfully')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Error updating .env')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getEditableKeys(): array
    {
        return [
            'APP_NAME', 'APP_LOCALE',
            'MAIL_MAILER', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME',
            'MAIL_PASSWORD', 'MAIL_ENCRYPTION', 'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME',
            'GOOGLE_CLIENT_ID', 'GOOGLE_CLIENT_SECRET', 'GOOGLE_REDIRECT_URI',
            'PAYMOB_BASE_URL', 'PAYMOB_API_KEY', 'PAYMOB_IFRAME_ID', 'PAYMOB_INTEGRATION_ID',
            'PAYMOB_PUBLIC_KEY', 'PAYMOB_NOTIFICATION_URL', 'PAYMOB_REDIRECTION_URL', 'PAYMOB_ENV',
            'JEEBLY_API_KEY','JEEBLY_CLIENT_KEY','JEEBLY_BASE_URL','JEEBLY_USERNAME','JEEBLY_PASSWORD',

        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

}
