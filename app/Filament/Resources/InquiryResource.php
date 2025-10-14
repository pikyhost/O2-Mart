<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages;
use App\Models\Inquiry;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $modelLabel = 'Inquiry';

    protected static ?string $navigationGroup = 'Customer Service';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Inquiry Details')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Info')
                            ->schema([
                                Forms\Components\Section::make('Basic Information')
                                    ->schema([
                                        Forms\Components\Select::make('type')
                                            ->options(Inquiry::TYPES)
                                            ->required(),
                                        // Forms\Components\Select::make('status')
                                        //     ->options(Inquiry::STATUSES)
                                        //     ->required(),
                                        // Forms\Components\Select::make('priority')
                                        //     ->options(Inquiry::PRIORITIES)
                                        //     ->required(),
                                        // Forms\Components\Select::make('assigned_to')
                                        //     ->label('Assigned To')
                                        //     ->options(User::all()->pluck('name', 'id'))
                                        //     ->searchable(),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Customer Info')
                            ->schema([
                                Forms\Components\Section::make('Customer Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('full_name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('phone_number')
                                            ->tel()
                                            ->maxLength(20),
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->maxLength(255),
                                        Forms\Components\Select::make('source')
                                            ->options([
                                                'website' => 'Website',
                                                'phone' => 'Phone',
                                                'email' => 'Email',
                                                'walk-in' => 'Walk-in',
                                            ]),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Vehicle Info')
                            ->schema([
                                Forms\Components\Section::make('Vehicle Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('car_make')
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('car_model')
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('car_year')
                                            ->numeric()
                                            ->minValue(1900)
                                            ->maxValue(now()->year),
                                        Forms\Components\TextInput::make('vin_chassis_number')
                                            ->label('VIN/Chassis Number')
                                            ->maxLength(50),
                                        Forms\Components\Select::make('rim_size_id')
                                            ->label('Rim Size')
                                            ->options(\App\Models\RimSize::all()->pluck('size', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->visible(fn (Forms\Get $get) => $get('type') === 'rims'),
    
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Parts Info')
                            ->schema([
                                Forms\Components\Section::make('Parts Information')
                                    ->schema([
                                        Forms\Components\TagsInput::make('required_parts')
                                            ->placeholder('Add part')
                                            ->separator(','),
                                        Forms\Components\TextInput::make('quantity')
                                            ->numeric()
                                            ->minValue(1),
                                        Forms\Components\Textarea::make('battery_specs')
                                            ->label('Battery Specifications')
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('description')
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('front_width'),
                                        Forms\Components\TextInput::make('front_height'),
                                        Forms\Components\TextInput::make('front_diameter'),
                                        TextInput::make('rear_tyres')
                                            ->label('Rear tyres')
                                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
                                            ->disabled()
                                            ->columnSpanFull()

                                    ])->columns(2),
                            ]),

                        // Forms\Components\Tabs\Tab::make('Pricing')
                        //     ->schema([
                        //         Forms\Components\Section::make('Pricing')
                        //             ->schema([
                        //                 Forms\Components\TextInput::make('quoted_price')
                        //                     ->numeric()
                        //                     ->prefix('$'),
                        //                 Forms\Components\DateTimePicker::make('quoted_at'),
                        //             ])->columns(2),
                        //     ]),

                        Forms\Components\Tabs\Tab::make('Media')
                            ->schema([
                                Forms\Components\Section::make('Media')
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('car_license_photos')
                                            ->label('Car License Photos')
                                            ->collection('car_license_photos')
                                            ->multiple()
                                            ->image()
                                            ->imagePreviewHeight('150')
                                            ->loadingIndicatorPosition('left')
                                            ->panelAspectRatio('2:1')
                                            ->panelLayout('integrated')
                                            ->removeUploadedFileButtonPosition('right')
                                            ->uploadButtonPosition('left')
                                            ->uploadProgressIndicatorPosition('left')
                                            ->columnSpanFull(),
                                        SpatieMediaLibraryFileUpload::make('part_photos')
                                            ->label('Part Photos')
                                            ->collection('part_photos')
                                            ->multiple()
                                            ->image()
                                            ->imagePreviewHeight('150')
                                            ->loadingIndicatorPosition('left')
                                            ->panelAspectRatio('2:1')
                                            ->panelLayout('integrated')
                                            ->removeUploadedFileButtonPosition('right')
                                            ->uploadButtonPosition('left')
                                            ->uploadProgressIndicatorPosition('left')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Admin Notes')
                            ->schema([
                                Forms\Components\Section::make('Admin Notes')
                                    ->schema([
                                        Forms\Components\Textarea::make('admin_notes')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString() // Optional: remembers the last active tab
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn (string $state): string => Str::title($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'rims' => 'info',
                        'auto_parts' => 'primary',
                        'battery' => 'warning',
                        'tires' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'info',
                        'quoted' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('page_source')
                    ->label('Page Source')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (?string $state): string => $state ? Str::title(str_replace('_', ' ', $state)) : 'Unknown')
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('Assigned To'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quoted_price')
                    ->money('USD')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(Inquiry::TYPES),
                Tables\Filters\SelectFilter::make('status')
                    ->options(Inquiry::STATUSES),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(Inquiry::PRIORITIES),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('page_source')
                    ->label('Page Source')
                    ->options([
                        'shop_page' => 'Shop Page',
                        'auto_parts_page' => 'Auto Parts Page',
                        'battery_page' => 'Battery Page',
                        'tires_page' => 'Tires Page',
                        'rims_page' => 'Rims Page',
                        'get_quote_page' => 'Get Quote Page',
                        'contact_page' => 'Contact Page',
                    ]),
                Tables\Filters\Filter::make('has_files')
                    ->label('Has Files')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('car_license_photos')->orWhereNotNull('part_photos')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    // Status Actions
                    Tables\Actions\Action::make('mark_pending')
                        ->label('Mark as Pending')
                        ->icon('heroicon-o-clock')
                        ->color('gray')
                        ->action(fn (Inquiry $record) => $record->update(['status' => 'pending']))
                        ->visible(fn (Inquiry $record) => $record->status !== 'pending'),
                    Tables\Actions\Action::make('mark_processing')
                        ->label('Mark as Processing')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->color('info')
                        ->action(fn (Inquiry $record) => $record->update(['status' => 'processing']))
                        ->visible(fn (Inquiry $record) => $record->status !== 'processing'),
                    Tables\Actions\Action::make('mark_quoted')
                        ->label('Mark as Quoted')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('warning')
                        ->action(fn (Inquiry $record) => $record->update(['status' => 'quoted']))
                        ->visible(fn (Inquiry $record) => $record->status !== 'quoted'),
                    Tables\Actions\Action::make('mark_completed')
                        ->label('Mark as Completed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Inquiry $record) => $record->update(['status' => 'completed']))
                        ->visible(fn (Inquiry $record) => $record->status !== 'completed'),
                    Tables\Actions\Action::make('mark_cancelled')
                        ->label('Mark as Cancelled')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Inquiry $record) => $record->update(['status' => 'cancelled']))
                        ->visible(fn (Inquiry $record) => $record->status !== 'cancelled'),
                    // Priority Actions
                    Tables\Actions\Action::make('priority_low')
                        ->label('Set Low Priority')
                        ->icon('heroicon-o-arrow-down')
                        ->color('gray')
                        ->action(fn (Inquiry $record) => $record->update(['priority' => 'low']))
                        ->visible(fn (Inquiry $record) => $record->priority !== 'low'),
                    Tables\Actions\Action::make('priority_medium')
                        ->label('Set Medium Priority')
                        ->icon('heroicon-o-minus')
                        ->color('info')
                        ->action(fn (Inquiry $record) => $record->update(['priority' => 'medium']))
                        ->visible(fn (Inquiry $record) => $record->priority !== 'medium'),
                    Tables\Actions\Action::make('priority_high')
                        ->label('Set High Priority')
                        ->icon('heroicon-o-arrow-up')
                        ->color('warning')
                        ->action(fn (Inquiry $record) => $record->update(['priority' => 'high']))
                        ->visible(fn (Inquiry $record) => $record->priority !== 'high'),
                    Tables\Actions\Action::make('priority_urgent')
                        ->label('Set Urgent Priority')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('danger')
                        ->action(fn (Inquiry $record) => $record->update(['priority' => 'urgent']))
                        ->visible(fn (Inquiry $record) => $record->priority !== 'urgent'),
                ])->label('Actions'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('bulk_mark_pending')
                        ->label('Mark as Pending')
                        ->icon('heroicon-o-clock')
                        ->color('gray')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => 
                            $records->each(fn (Inquiry $record) => $record->update(['status' => 'pending']))
                        ),
                    Tables\Actions\BulkAction::make('bulk_mark_processing')
                        ->label('Mark as Processing')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->color('info')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => 
                            $records->each(fn (Inquiry $record) => $record->update(['status' => 'processing']))
                        ),
                    Tables\Actions\BulkAction::make('bulk_mark_quoted')
                        ->label('Mark as Quoted')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('warning')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => 
                            $records->each(fn (Inquiry $record) => $record->update(['status' => 'quoted']))
                        ),
                    Tables\Actions\BulkAction::make('bulk_mark_completed')
                        ->label('Mark as Completed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => 
                            $records->each(fn (Inquiry $record) => $record->update(['status' => 'completed']))
                        ),
                    Tables\Actions\BulkAction::make('bulk_mark_cancelled')
                        ->label('Mark as Cancelled')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => 
                            $records->each(fn (Inquiry $record) => $record->update(['status' => 'cancelled']))
                        ),
                    Tables\Actions\BulkAction::make('bulk_priority_low')
                        ->label('Set Low Priority')
                        ->icon('heroicon-o-arrow-down')
                        ->color('gray')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => 
                            $records->each(fn (Inquiry $record) => $record->update(['priority' => 'low']))
                        ),
                    Tables\Actions\BulkAction::make('bulk_priority_medium')
                        ->label('Set Medium Priority')
                        ->icon('heroicon-o-minus')
                        ->color('info')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => 
                            $records->each(fn (Inquiry $record) => $record->update(['priority' => 'medium']))
                        ),
                    Tables\Actions\BulkAction::make('bulk_priority_high')
                        ->label('Set High Priority')
                        ->icon('heroicon-o-arrow-up')
                        ->color('warning')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => 
                            $records->each(fn (Inquiry $record) => $record->update(['priority' => 'high']))
                        ),
                    Tables\Actions\BulkAction::make('bulk_priority_urgent')
                        ->label('Set Urgent Priority')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('danger')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => 
                            $records->each(fn (Inquiry $record) => $record->update(['priority' => 'urgent']))
                        ),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInquiries::route('/'),
            'create' => Pages\CreateInquiry::route('/create'),
            'edit' => Pages\EditInquiry::route('/{record}/edit'),
            'view' => Pages\ViewInquiry::route('/{record}'),
        ];
    }
}
