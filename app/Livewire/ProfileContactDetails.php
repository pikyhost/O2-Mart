<?php

namespace App\Livewire;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class ProfileContactDetails extends MyProfileComponent implements HasActions, HasForms
{
    protected string $view = 'livewire.profile-contact-details';

    public static $sort = 2;

    public array $only = ['phone', 'second_phone'];

    public array $data;

    public $user;

    public $userClass;

    public function mount()
    {
        $this->user = auth()->user();

        if (!$this->user) {
            abort(403, 'User not authenticated');
        }

        $this->userClass = get_class($this->user);

        // Ensure 'addresses' relationship is eager-loaded to prevent issues
        $this->user->load('addresses');

        $this->form->fill($this->user->only($this->only));
    }


    public function form(Form $form): Form
    {
        return $form
            ->model($this->user) // Bind the form to the user model
            ->schema([
                PhoneInput::make('phone')
                    ->separateDialCode(true) // Shows flag and +20 separately
                    ->enableIpLookup(true) // Enable IP-based country detection
                    ->nullable()
                    ->rules([
                        'min:11',
                        'max:20',
                        Rule::unique('users', 'phone')
                            ->where(function ($query) {
                                return $query->where('id', '!=', Auth::id());
                            }),
                        Rule::unique('users', 'second_phone')
                            ->where(function ($query) {
                                return $query->where('id', '!=', Auth::id());
                            }),
                    ])
                    ->label(__('Phone'))
                    ->columnSpanFull(),

                PhoneInput::make('second_phone')
                    ->different('phone')
                    ->separateDialCode(true) // Shows flag and +20 separately
                    ->enableIpLookup(true) // Enable IP-based country detection
                    ->nullable()
                    ->rules([
                        'min:11',
                        'max:20',
                        Rule::unique('users', 'phone')
                            ->where(function ($query) {
                                return $query->where('id', '!=', Auth::id());
                            }),
                        Rule::unique('users', 'second_phone')
                            ->where(function ($query) {
                                return $query->where('id', '!=', Auth::id());
                            }),
                    ])
                    ->label(__('Secondary Phone'))
                    ->columnSpanFull(),

                Repeater::make('addresses')
                    ->relationship('addresses', fn () => $this->user->addresses())
                    ->label(__('Addresses'))
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('label')
                            ->label(__('Label (e.g., Home, Work, Garage)'))
                            ->required(),

                        TextInput::make('full_name')
                            ->label(__('Full Name'))
                            ->required(),

                        TextInput::make('phone')
                            ->label(__('Phone'))
                            ->tel()
                            ->nullable(),

                        Textarea::make('address_line_1')
                            ->label(__('Address Line 1'))
                            ->required(),

                        Textarea::make('address_line_2')
                            ->label(__('Address Line 2'))
                            ->nullable(),

                        Select::make('country_id')
                            ->label(__('Country'))
                            ->options(fn () => \App\Models\Country::pluck('name', 'id'))
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => [
                                $set('governorate_id', null),
                                $set('city_id', null),
                                $set('area_id', null),
                            ])
                            ->required(),

                        Select::make('governorate_id')
                            ->label(__('Governorate'))
                            ->options(fn (Get $get) => \App\Models\Governorate::where('country_id', $get('country_id'))->pluck('name', 'id'))
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('city_id', null))
                            ->required(),

                        Select::make('city_id')
                            ->label(__('City'))
                            ->options(fn (Get $get) => \App\Models\City::where('governorate_id', $get('governorate_id'))->pluck('name', 'id'))
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('area_id', null))
                            ->required(),

                        Select::make('area_id')
                            ->label(__('Area'))
                            ->options(fn (Get $get) => \App\Models\Area::where('city_id', $get('city_id'))->pluck('name', 'id'))
                            ->nullable(),

                        TextInput::make('postal_code')
                            ->label(__('Postal Code'))
                            ->nullable(),

                        Textarea::make('additional_info')
                            ->label(__('Additional Information'))
                            ->nullable(),

                        Checkbox::make('is_primary')
                            ->label(__('Primary Address'))
                            ->default(false),
                    ])
                    ->columns(1)
                    ->addable()
                    ->deletable()
                    ->reorderable()
                    ->defaultItems(1)
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = collect($this->form->getState())->only($this->only)->all();
        $this->user->update($data);

        Notification::make()
            ->success()
            ->title(__('profile.update_success'))
            ->send();
    }

    public function submitFormAction(): Action
    {
        return Action::make('submit')
            ->label(__('Update'))
            ->submit('submit');
    }
}
