<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Traits\HasCreatedAtFilter;
use App\Traits\HasTimestampSection;
use Filament\Events\Auth\Registered;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;

class UserResource extends Resource
{
    use HasCreatedAtFilter, HasTimestampSection;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('Users');
    }

    public static function getModelLabel(): string
    {
        return __('User');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings Management'); //Products Management
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('Users');
    }

    public static function getLabel(): ?string
    {
        return __('User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Users');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewUser::class,
            Pages\EditUser::class,
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            self::getMainSection(),
            self::getTimestampSection(),
        ])
            ->columns(3);
    }

    private static function getMainSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make()->schema([
            Forms\Components\FileUpload::make('avatar_url')
                ->label(__('Upload avatar image'))
                ->image()
                ->avatar()
                ->imageEditor()
                ->maxSize(10 * 1024 * 1024)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('name')
                ->label(__('name'))
                ->required()
                ->maxLength(255)
                ->dehydrateStateUsing(fn (string $state): string => ucwords($state)),

            TextInput::make('email')
                ->label(__('Email address'))
                ->required()
                ->email()
               
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->afterStateUpdated(function ($state, $livewire) {
                    if ($livewire->record && $livewire->record->email !== $state) {
                        event(new Registered($livewire->record));
                    }
                }),

            Forms\Components\TextInput::make('phone')
                ->label(__('Phone Number'))
                ->required()
                ->rules(function ($get, $state, $livewire) {
                    $recordId = $livewire->record->id ?? null;
                    return [
                        'min:11',
                        'max:20',
                        Rule::unique('users', 'phone')->ignore($recordId),
                        Rule::unique('users', 'second_phone')->ignore($recordId),
                    ];
                }),

            // PhoneInput::make('second_phone')
            //     ->nullable()
            //     ->different('phone')
            //     ->rules(function ($get) {
            //         $recordId = request()->route('record'); 
            //         return [
            //             'nullable',
            //             'min:11',
            //             'max:20',
            //             Rule::unique('users', 'phone')->ignore($recordId),
            //             Rule::unique('users', 'second_phone')->ignore($recordId),
            //         ];
            //     })
            //     ->label(__('Second Phone Number')),


            Forms\Components\TextInput::make('password')
                ->label(__('Password'))
                ->helperText(function () {
                    $baseText = 'Minimum 8 characters, uppercase, lowercase, and symbols required';
                    $editText = 'Leave blank if you do not wish to change current password';
                    return request()->is('admin/users/create') ? $baseText : $baseText . '. ' . $editText;
                })
                ->password()
                ->rules([
                    \Illuminate\Validation\Rules\Password::defaults()
                ])
                ->live()
                ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component, $state) {
                    if (filled($state)) {
                        $livewire->validateOnly($component->getStatePath());
                    }
                })
                ->revealable()
                ->autocomplete(false)
                ->maxLength(255)
                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create'),

            Forms\Components\TextInput::make('password_confirmation')
                ->label(__('Confirm Password'))
                ->password()
                ->revealable()
                ->autocomplete(false)
                ->maxLength(255)
                ->requiredWith('password')
                ->same('password')
                ->live()
                ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component, $state) {
                    if (filled($state)) {
                        $livewire->validateOnly($component->getStatePath());
                    }
                })
                ->dehydrated(false),

            Forms\Components\Select::make('roles')
                ->searchable()
                ->preload()
                ->columnSpanFull()
                ->multiple()
                ->maxItems(1)
                ->label(__('roles'))
                ->relationship('roles', 'name', fn ($query) =>
                auth()->user()?->hasRole('super_admin')
                    ? $query
                    : $query->where('name', '!=', 'super_admin')
                ),

            Forms\Components\DateTimePicker::make('email_verified_at')
                ->columnSpanFull()
                ->default(now())
                ->label(__('Verified at'))
        ])
            ->columns(2)
            ->columnSpan([
                'lg' => fn (?User $record) => $record === null ? 3 : 2,
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label(__('Avatar'))
                    ->defaultImageUrl(function ($record) {
                        $nameParts = explode(' ', trim($record->name));
                        $initials = count($nameParts) === 1
                            ? mb_strtoupper(mb_substr($nameParts[0], 0, 1))
                            : mb_strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1));
                        return 'https://ui-avatars.com/api/?background=000000&color=fff&name=' . urlencode($initials);
                    })
                    ->circular()
                    ->grow(false),

                TextColumn::make('id')
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn($state) => '#'.$state)
                    ->label(__('ID'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('name'))
                    ->tooltip(fn (TextColumn $column): ?string => static::getTooltip($column))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('Email address'))
                    ->tooltip(fn (TextColumn $column): ?string => static::getTooltip($column))
                    ->searchable()
                    ->iconColor('primary')
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('phone')
                    ->iconColor('primary')
                    ->icon('heroicon-o-phone')
                    ->label(__('Phone'))
                    ->placeholder(__('No phone number saved'))
                    ->searchable(),

                TextColumn::make('second_phone')
                    ->iconColor('primary')
                    ->icon('heroicon-o-phone')
                    ->label(__('Second Phone'))
                    ->placeholder(__('No phone number saved'))
                    ->searchable(),

                TextColumn::make('roles.name')
                    ->label(__('roles'))
                    ->placeholder(__('No role now'))
                    ->formatStateUsing(fn (string $state) => UserRole::getLabelFor($state))
                    ->badge()
                    ->color(fn (string $state) => UserRole::getColorFor($state)),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label(__("Active")),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->placeholder(__('No verified yet'))
                    ->label(__("Verified")),

                TextColumn::make('created_at')
                    ->label(__('created_at'))
                    ->toggleable(true, true)
                    ->dateTime(),
                TextColumn::make('updated_at')
                    ->label(__('Last Modified At'))
                    ->dateTime(),
                TextColumn::make('last_filter')
                    ->label(__('Saved Filters'))
                    ->limit(40)
                    ->tooltip(fn ($record) => is_array($record->last_filter) ? json_encode($record->last_filter) : $record->last_filter)
                    ->toggleable(true, true),    
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label(__('User roles'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn (Role $record) => Str::headline($record->name))
                    ->relationship('roles', 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('name', '!=', 'panel_user'))
                    ->columnSpan(['sm' => 2, 'md' => 2, 'lg' => 2, 'xl' => 2, '2xl' => 2, 'default' => 2]),
                DateFilter::make('created_at')
                    ->columnSpan(['sm' => 2, 'md' => 2, 'lg' => 2, 'xl' => 2, '2xl' => 2, 'default' => 2])
                    ->label(__('Creation date')),
                Filter::make('is_active')->toggle()->label(__("Is Active?"))->columnSpanFull(),
            ], Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(4)
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label(__('View')),
                    Tables\Actions\EditAction::make()->color('primary')->label(__('Edit')),
                    DeleteAction::make()
                        ->label(__('Delete'))
                        ->hidden(fn($record) => $record->hasRole('super_admin') &&
                            auth()->user()->hasRole('admin')),

                    Action::make('active')
                        ->hidden(fn($record) => $record->is_active) // Use enum comparison
                        ->label(__('Activate'))
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn ($record) => self::activeUser($record)),

                    Action::make('block')
                        ->hidden(fn($record) =>
                            !$record->is_active ||
                            ($record->hasRole('super_admin') && auth()->user()->hasRole('admin'))
                        )
                        ->label(__('Block'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($record) => self::blockUser($record)),
                ])
                    ->label(__('Actions'))
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalHeading(__('Delete Selected Users'))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $currentUser = auth()->user();

                            // Separate super_admin users
                            $superAdminUsers = $records->filter(fn ($record) => $record->hasRole('super_admin'));
                            $normalUsers = $records->reject(fn ($record) => $record->hasRole('super_admin'));

                            // Prevent admin from deleting super_admin users
                            if ($currentUser->hasRole('admin') && $superAdminUsers->isNotEmpty()) {
                                Notification::make()
                                    ->title(__('Action Denied'))
                                    ->danger()
                                    ->body(__('You cannot delete users with the Super Admin role.'))
                                    ->send();
                            }

                            // Proceed with deletion only for normal users
                            if ($normalUsers->isNotEmpty()) {
                                $deletedCount = $normalUsers->count();
                                $normalUsers->each->delete();

                                $locale = app()->getLocale();

                                $message = $locale === 'ar'
                                    ? "تم حذف {$deletedCount} مستخدم" . ($deletedCount > 1 ? 'ين' : '')
                                    : "{$deletedCount} user" . ($deletedCount > 1 ? 's' : '') . " have been deleted.";

                                Notification::make()
                                    ->title($locale === 'ar' ? 'تم الحذف' : 'Users Deleted')
                                    ->success()
                                    ->body($message)
                                    ->send();
                            }
                        }),

                    BulkAction::make('block')
                        ->label(__('Block Selected'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $currentUser = auth()->user();

                            // Separate super_admin users
                            $superAdminUsers = $records->filter(fn ($record) => $record->hasRole('super_admin'));
                            $normalUsers = $records->reject(fn ($record) => $record->hasRole('super_admin'));

                            // Prevent admin from blocking super_admin users
                            if ($currentUser->hasRole('admin') && $superAdminUsers->isNotEmpty()) {
                                Notification::make()
                                    ->title(__('Action Denied'))
                                    ->danger()
                                    ->body(__('You cannot block users with the Super Admin role.'))
                                    ->send();
                            }

                            // Proceed with blocking only for normal users
                            if ($normalUsers->isNotEmpty()) {
                                $normalUsers->each(fn ($record) => self::blockUser($record));

                                Notification::make()
                                    ->title(__('Users Blocked'))
                                    ->success()
                                    ->body(__(':count users have been blocked.', ['count' => $normalUsers->count()]))
                                    ->send();
                            }
                        }),

                    \Filament\Tables\Actions\BulkAction::make('active')
                        ->label(__('Activate Selected'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn ($record) => self::activeUser($record));
                        }),
                ])
            ])
            ->recordUrl(fn () => '');
    }

    private static function activeUser(User $record)
    {
        $record->update(['is_active' => true]);
    }

    private static function blockUser(User $record)
    {
        $record->update(['is_active' => false]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()->schema([
                \Filament\Infolists\Components\Split::make([
                    Grid::make(2)->schema([
                        Group::make([
                            TextEntry::make('id')
                                ->badge()
                                ->color('info')
                                ->label(__('ID')),
                            TextEntry::make('name')->label(__('name')),


                            PhoneEntry::make('phone')
                                ->placeholder(__('No phone number saved'))
                                ->label(__('Phone number'))
                                ->countryColumn('phone_country'),

                            PhoneEntry::make('second_phone')
                                ->placeholder(__('No phone number saved'))
                                ->label(__('Second Phone Number'))
                                ->countryColumn('phone_country'),

                            TextEntry::make('created_at')
                                ->label(__('Creation date'))
                                ->dateTime('D, M j, Y \a\t g:i A'),
                        ]),
                        Group::make([
                            TextEntry::make('roles.name')
                                ->label(__('roles'))
                                ->placeholder(__('No role now'))
                                ->formatStateUsing(fn (string $state) => UserRole::getLabelFor($state))
                                ->badge()
                                ->color(fn (string $state) => UserRole::getColorFor($state)),

                            IconEntry::make('id')
                                ->label(__('Is active'))
                                ->boolean(),

                            TextEntry::make('email')
                                ->label(__('Email address')),
                            TextEntry::make('updated_at')
                                ->label(__('Last modified at'))
                                ->dateTime('D, M j, Y \a\t g:i A'),
                        ]),
                    ]),

                    Section::make(__('Saved Filters'))
                        ->collapsed()
                        ->collapsible()
                        ->schema([
                            TextEntry::make('last_filter')
                                ->label(__('Last Filter JSON'))
                                ->formatStateUsing(fn($state) =>
                                    is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state
                                )
                                ->copyable()
                                // ->code()
                                ->columnSpanFull(),
                        ]),
                        
                    Section::make(__('Garage'))
                        ->collapsed()
                        ->collapsible()
                        ->schema([
                            \Filament\Infolists\Components\RepeatableEntry::make('userVehicles')
                                ->schema([
                                    TextEntry::make('make.name')
                                        ->label(__('Make')),
                                    TextEntry::make('model.name')
                                        ->label(__('Model')),
                                    TextEntry::make('car_year')
                                        ->label(__('Year')),
                                    TextEntry::make('vin')
                                        ->label(__('VIN'))
                                        ->placeholder(__('Not specified')),
                                    TextEntry::make('mileage')
                                        ->label(__('Mileage'))
                                        ->placeholder(__('Not specified'))
                                        ->suffix(' km'),
                                    TextEntry::make('created_at')
                                        ->label(__('Added'))
                                        ->dateTime(),
                                    TextEntry::make('updated_at')
                                        ->label(__('Last Modified At'))
                                        ->dateTime(),
                                ])
                                ->label(__('User Vehicles'))
                                ->columns(3),
                        ]),
                        
                    ImageEntry::make('avatar_url')
                        ->hiddenLabel()
                        ->grow(false),
                ])->from('xl'),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['id', 'name', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'ID' => $record->id,
            'Name' => $record->name,
            'Email' => $record->email,
        ]);
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return UserResource::getUrl('view', ['record' => $record]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Only super_admins can see super_admins
        if (!auth()->user()->hasRole('super_admin')) {
            $query->whereDoesntHave('roles', function($q) {
                $q->where('name', 'super_admin');
            });
        }

        return $query;
    }

    protected static function getTooltip(TextColumn $column): ?string
    {
        return strlen($column->getState()) > $column->getCharacterLimit() ? $column->getState() : null;
    }

    public static function canAccess(): bool
    {
        return true;
    }
}
