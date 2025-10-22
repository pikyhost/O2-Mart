<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MobileVanServiceResource\Pages;
use App\Filament\Resources\MobileVanServiceResource\Pages\ManageMobileVanServiceWorkingHour;
use App\Models\MobileVanService;
use App\Models\Day;
use App\Models\WorkingHour;
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\BaseResource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class MobileVanServiceResource extends BaseResource
{
    protected static ?string $model = MobileVanService::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'Service Points';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditMobileVanService::class,
            ManageMobileVanServiceWorkingHour::class,
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Main Information')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('location')
                                    ->required(),
                                TextInput::make('google_map_link')
                                    ->label('Google Map Link')
                                    ->url()
                                    ->prefixIcon('heroicon-o-globe-asia-australia')
                                    ->placeholder('Enter Google Maps URL')
                                    ->maxLength(2048)
                                    ->columnSpanFull()
                                    ->helperText('Paste the full Google Maps URL (e.g., https://maps.google.com/...)')
                                    ->nullable(),
                                Checkbox::make('is_active')
                                    ->default(true),
                                Select::make('productTypes')
                                    ->label('Supported Product Types')
                                    ->multiple()
                                    ->relationship('productTypes', 'name')
                                    ->options(\App\Models\ProductType::pluck('name', 'id'))
                                    ->preload()
                                    ->searchable()
                                    ->columnSpanFull(),

                            ])
                            ->columns(1),

                        Tab::make('Branches')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Repeater::make('branches')
                                    ->relationship('branches')
                                    ->schema([
                                        TextInput::make('name')->required(),
                                        TextInput::make('address')->nullable(),
                                        TextInput::make('google_map_link')
                                            ->label('Google Map Link')
                                            ->url()
                                            ->prefixIcon('heroicon-o-globe-asia-australia')
                                            ->placeholder('Enter Google Maps URL')
                                            ->maxLength(2048)
                                            ->columnSpanFull()
                                            ->helperText('Paste the full Google Maps URL (e.g., https://maps.google.com/...)')
                                            ->nullable(),
                                    ])
                                    ->columns(1)
                                    ->collapsible()
                                    ->label('Branches'),
                            ])->columnSpanFull(), // Optional: to make the tabs span full width

                        Tab::make('Working Hours')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Repeater::make('workingHours')
                                    ->relationship()
                                    ->schema([
                                        Select::make('day_ids')
                                            ->multiple()
                                            ->label('Days')
                                            ->options(Day::pluck('name', 'id'))
                                            ->required()
                                            ->columnSpanFull()
                                            ->rules([
                                                function (Get $get, $record) {
                                                    return function (string $attribute, $value, Closure $fail) use ($get, $record) {
                                                        if ($get('is_closed')) return;

                                                        $centerId = $get('installer_shop_id') ?? $record?->installer_shop_id;
                                                        $gasStationId = $get('mobile_van_service_id') ?? $record?->mobile_van_service_id;

                                                        $existingHours = WorkingHour::query()
                                                            ->where(function ($query) use ($centerId, $gasStationId) {
                                                                $query->where('installer_shop_id', $centerId)
                                                                    ->orWhere('mobile_van_service_id', $gasStationId);
                                                            })
                                                            ->get();

                                                        $currentSelectedDayIds = $value ?? [];

                                                        foreach ($currentSelectedDayIds as $dayId) {
                                                            // Check if this day exists in the DB for the location
                                                            $conflicting = $existingHours
                                                                ->where('day_id', $dayId)
                                                                ->filter(function ($hour) use ($get) {
                                                                    return !in_array($hour->day_id, $get('day_ids') ?? []);
                                                                })
                                                                ->first();

                                                            if ($conflicting) {
                                                                $dayName = Day::find($dayId)?->name;
                                                                $fail("The day '{$dayName}' already has working hours for the selected location.");
                                                            }
                                                        }
                                                    };
                                                },
                                            ])

                                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? $state : [$state])
                                            ->getOptionLabelsUsing(fn ($value): array => !is_array($value)
                                                ? [Day::find($value)?->name]
                                                : Day::whereIn('id', $value)->pluck('name')->toArray()),

                                        Toggle::make('is_closed')
                                            ->label('Closed for the entire day?')
                                            ->live()
                                            ->columnSpanFull(),

                                        TimePicker::make('opening_time')
                                            ->dehydrated(fn (Get $get) => !$get('is_closed'))
                                            ->required()
                                            ->hidden(fn (Get $get) => $get('is_closed'))
                                            ->rules([
                                                fn (Get $get) => function (string $attribute, $value, Closure $fail) use ($get) {
                                                    if ($get('is_closed')) return;
                                                    if ($value && $get('closing_time') && $value >= $get('closing_time')) {
                                                        $fail('Opening time must be before closing time');
                                                    }
                                                }
                                            ]),

                                        TimePicker::make('closing_time')
                                            ->dehydrated(fn (Get $get) => !$get('is_closed'))
                                            ->required()
                                            ->hidden(fn (Get $get) => $get('is_closed'))
                                            ->rules([
                                                fn (Get $get) => function (string $attribute, $value, Closure $fail) use ($get) {
                                                    if ($get('is_closed')) return;
                                                    if ($value && $get('opening_time') && $value <= $get('opening_time')) {
                                                        $fail('Closing time must be after opening time');
                                                    }
                                                }
                                            ]),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(1)
                                    ->reorderable()
                                    ->addActionLabel('Add Working Hours')
                                    ->itemLabel(fn (array $state): ?string =>
                                    isset($state['day_ids'])
                                        ? implode(', ', Day::whereIn('id', (array)$state['day_ids'])->pluck('name')->toArray())
                                        : null)
                                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                        return [
                                            'day_id' => Arr::first($data['day_ids']), // Store first day for relationship
                                            'day_ids' => $data['day_ids'], // Keep all days for processing
                                            'is_closed' => $data['is_closed'],
                                            'opening_time' => $data['opening_time'],
                                            'closing_time' => $data['closing_time'],
                                        ];
                                    })
                                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                        return [
                                            'day_id' => Arr::first($data['day_ids']), // Maintain relationship
                                            'day_ids' => $data['day_ids'], // Keep all days
                                            'is_closed' => $data['is_closed'],
                                            'opening_time' => $data['opening_time'],
                                            'closing_time' => $data['closing_time'],
                                        ];
                                    })
                                    ->loadStateFromRelationshipsUsing(function (Repeater $component): void {
                                        $records = $component->getRelationship()->getResults();

                                        $groupedRecords = $records->groupBy(function ($record) {
                                            return implode('|', [
                                                $record->opening_time,
                                                $record->closing_time,
                                                $record->is_closed,
                                            ]);
                                        });

                                        $component->state(
                                            $groupedRecords->map(function (Collection $group) {
                                                $first = $group->first();
                                                return [
                                                    'day_ids' => $group->pluck('day_id')->toArray(),
                                                    'is_closed' => $first->is_closed,
                                                    'opening_time' => $first->opening_time,
                                                    'closing_time' => $first->closing_time,
                                                ];
                                            })->values()->toArray()
                                        );
                                    })
                                    ->saveRelationshipsUsing(function (Repeater $component, Model $record, array $state) {
                                        $relationship = $component->getRelationship();
                                        $relationship->delete();
                                        
                                        $configuredDays = [];
                                        
                                        foreach ($state as $item) {
                                            $dayIds = is_array($item['day_ids'] ?? null) ? $item['day_ids'] : [];
                                            $isClosed = $item['is_closed'] ?? false;
                                            $openingTime = $isClosed ? null : ($item['opening_time'] ?? null);
                                            $closingTime = $isClosed ? null : ($item['closing_time'] ?? null);

                                            foreach ($dayIds as $dayId) {
                                                $relationship->create([
                                                    'day_id' => (int) $dayId,
                                                    'is_closed' => (bool) $isClosed,
                                                    'opening_time' => $openingTime,
                                                    'closing_time' => $closingTime,
                                                ]);
                                                $configuredDays[] = (int) $dayId;
                                            }
                                        }
                                        
                                        for ($dayId = 1; $dayId <= 7; $dayId++) {
                                            if (!in_array($dayId, $configuredDays)) {
                                                $relationship->create([
                                                    'day_id' => $dayId,
                                                    'is_closed' => true,
                                                    'opening_time' => null,
                                                    'closing_time' => null,
                                                ]);
                                            }
                                        }
                                    })
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('location')->limit(50) ->searchable(),
                IconColumn::make('is_active')->boolean()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMobileVanServices::route('/'),
            'create' => Pages\CreateMobileVanService::route('/create'),
            'edit' => Pages\EditMobileVanService::route('/{record}/edit'),
            'workingHours' => ManageMobileVanServiceWorkingHour::route('/{record}/working-hours'),
        ];
    }
}
