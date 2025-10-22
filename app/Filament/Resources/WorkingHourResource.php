<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkingHourResource\Pages;
use App\Models\WorkingHour;
use Closure;
use Filament\Forms\Form;
use Filament\Forms\Get;
use App\Filament\Resources\BaseResource;
use App\Models\MobileVanService;
use App\Models\Day;
use App\Models\InstallerShop;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webbingbrasil\FilamentAdvancedFilter\Filters\BooleanFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;

class WorkingHourResource extends BaseResource
{
    protected static ?string $model = WorkingHour::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Business Hours';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Location Selection (MobileVanService/Gas Station)
                Select::make('installer_shop_id')
                    ->columnSpanFull()
                    ->label('MobileVanService')
                    ->options(MobileVanService::pluck('name', 'id'))
                    ->nullable()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('mobile_van_service_id', null))
                    ->hidden(fn (callable $get) => $get('mobile_van_service_id') !== null),

                Select::make('mobile_van_service_id')
                    ->columnSpanFull()
                    ->label('Gas Station')
                    ->options(InstallerShop::pluck('name', 'id'))
                    ->nullable()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('installer_shop_id', null))
                    ->hidden(fn (callable $get) => $get('installer_shop_id') !== null),

                Select::make('day_id')
                    ->columnSpanFull()
                    ->label('Day')
                    ->options(Day::pluck('name', 'id'))
                    ->required()
                    ->rules([
                        function (Get $get, $record) { // Add $record
                            return function (string $attribute, $value, Closure $fail) use ($get, $record) {
                                if ($get('is_closed')) return; // Skip validation if closed

                                $centerId = $get('installer_shop_id');
                                $gasStationId = $get('mobile_van_service_id');

                                $query = WorkingHour::where('day_id', $value)
                                    ->where(function ($query) use ($centerId, $gasStationId) {
                                        $query->where('installer_shop_id', $centerId)
                                            ->orWhere('mobile_van_service_id', $gasStationId);
                                    });

                                if ($record && $record->id) {
                                    // Exclude the current record when editing
                                    $query->where('id', '!=', $record->id);
                                }

                                if ($query->exists()) {
                                    $fail('This day already has working hours for the selected location.');
                                }
                            };
                        },
                    ]),

        // Closed Toggle (Master Switch)
                Toggle::make('is_closed')
                    ->label('Closed for the entire day?')
                    ->live()
                    ->columnSpanFull(),

                // Time Fields (Conditional)
                TimePicker::make('opening_time')
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
            ]);
    }

    // php artisan make:filament-page ManageMobileVanServiceWorkingHour --resource=MobileVanServiceResource --type=ManageRelatedRecords
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('center.name')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('gasStation.name')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('day.name')
                    ->searchable()
                    ->badge(),
                TextColumn::make('opening_time') ->placeholder('-'),
                TextColumn::make('closing_time') ->placeholder('-'),
                TextColumn::make('is_closed')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'danger' : 'success')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Closed' : 'Open'),
            ])
            ->filters([
                SelectFilter::make('installer_shop_id')
                    ->label('MobileVanService')
                    ->relationship('center', 'name')
                    ->searchable(),

                SelectFilter::make('mobile_van_service_id')
                    ->label('Gas Station')
                    ->relationship('gasStation', 'name')
                    ->searchable(),

                SelectFilter::make('day_id')
                    ->label('Day')
                    ->relationship('day', 'name')
                    ->searchable(),

                Filter::make('opening_time')
                    ->form([
                        TimePicker::make('opening_time')
                            ->label('Opening Time'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['opening_time']) {
                            $query->where('opening_time', '>=', $data['opening_time']);
                        }
                    }),

                Filter::make('closing_time')
                    ->form([
                        TimePicker::make('closing_time')
                            ->label('Closing Time'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['closing_time']) {
                            $query->where('closing_time', '<=', $data['closing_time']);
                        }
                    }),

                TernaryFilter::make('is_closed')
                    ->label('Status')
                    ->trueLabel('Closed')
                    ->falseLabel('Open'),
            ])

            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWorkingHours::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return false; // TODO: Change the autogenerated stub
    }
}
