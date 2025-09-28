<?php

namespace App\Filament\Resources;

use App\Filament\Exports\TyreExporter;
use App\Filament\Imports\TyreImporter;
use App\Filament\Resources\TyreResource\Pages;
use App\Models\Brand;
use App\Models\Tyre;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\ExportAction;

class TyreResource extends Resource
{
    protected static ?string $model = Tyre::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Tyres';
    protected static ?int $navigationSort = 27;
    protected static ?string $navigationLabel = 'Tyres';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tyre Tabs')
                    ->tabs([
                        Tab::make(__('General Info'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('title')
                                    ->label(__('Title'))
                                    ->required()
                                    ->maxLength(255)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) =>
                                        $set('slug', \Illuminate\Support\Str::slug($state))
                                    ),

                                TextInput::make('slug')
                                    ->label(__('Slug'))
                                    ->disabled()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                TextInput::make('sku')
                                    ->label(__('SKU'))
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                MarkdownEditor::make('description')
                                    ->label('Description')
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),

                        Tab::make(__('Media'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('tyre_feature_image')
                                    ->label(__('Feature Image'))
                                    ->collection('tyre_feature_image')
                                    ->image()
                                    ->maxSize(5120),

                                SpatieMediaLibraryFileUpload::make('tyre_secondary_image')
                                    ->label(__('Secondary Image'))
                                    ->collection('tyre_secondary_image')
                                    ->image()
                                    ->maxSize(5120),

                                SpatieMediaLibraryFileUpload::make('tyre_gallery')
                                    ->maxFiles(20)
                                    ->label(__('Gallery'))
                                    ->collection('tyre_gallery')
                                    ->multiple()
                                    ->acceptedFileTypes(['video/mp4', 'video/mpeg', 'video/quicktime',
                                        'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/jpg'])
                                    ->imageEditor()
                                    ->reorderable(),
                            ]),

                        Tab::make(__('Specifications'))
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Select::make('tyre_size_id')
                                    ->label('Tyre Size')
                                    ->relationship('tyreSize', 'size')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                TextInput::make('width')
                                    ->label(__('Width (mm)'))
                                    ->numeric()
                                    ->maxLength(10),
                                TextInput::make('height')
                                    ->label(__('Height (mm)'))
                                    ->numeric()
                                    ->maxLength(10),
                                TextInput::make('wheel_diameter')
                                    ->label('Wheel Diameter')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->step(0.1)
                                    ->rules(['required', 'numeric', 'min:10', 'max:30'])
                                    ->suffix('inch')
                                    ->formatStateUsing(fn ($state) => $state ? (fmod($state, 1) == 0 ? (int)$state : $state) : $state)
                                    ->required(),
                                Select::make('tyre_model_id')
                                    ->label('Tyre Model')
                                    ->relationship('tyreModel', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                TextInput::make('load_index')
                                    ->label(__('Load Index'))
                                    ->maxLength(10),
                                TextInput::make('speed_rating')
                                    ->label(__('Speed Rating'))
                                    ->maxLength(10),
                                TextInput::make('weight_kg')
                                    ->label(__('Weight (kg)'))
                                    ->numeric()
                                    ->step(0.01),
                                Select::make('tyre_attribute_id')
                                    ->label('Tyre Attribute')
                                    ->relationship('tyreAttribute', 'tyre_attribute')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                                        $record->tyre_attribute . 
                                        ($record->rare_attribute ? ' - ' . $record->rare_attribute : '')
                                    )
                                    ->searchable()
                                    ->preload()

                            ])->columns(2),

                        Tab::make(__('Additional Info'))
                            ->icon('heroicon-o-document-text')
                            ->schema([

                                Select::make('production_year')
                                    ->label(__('Production Year'))
                                    ->options(function () {
                                        $years = range(date('Y'), date('Y') - 20);
                                        return array_combine($years, $years);
                                    }),
                                Select::make('tyre_country_id')
                                    ->label('Tyre Country')
                                    ->relationship('tyreCountry', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                                TextInput::make('warranty')
                                    ->label(__('Warranty'))
                                    ->maxLength(255),

                            ])->columns(2),

                        Tab::make(__('Pricing'))
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                        TextInput::make('price_vat_inclusive')
                                        ->label(__('Price (VAT Inclusive)'))
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('AED')
                                        ->nullable()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                            $price = floatval($state);
                                            $discount = floatval($get('discount_percentage'));

                                            if ($price && $discount > 0) {
                                                $discounted = $price - ($price * ($discount / 100));
                                                $set('discounted_price', round($discounted, 2));
                                            }
                                        }),

                                    TextInput::make('discount_percentage')
                                        ->label('Discount %')
                                        ->numeric()
                                        ->suffix('%')
                                        ->nullable()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                            $price = floatval($get('price_vat_inclusive'));
                                            $discount = floatval($state);

                                            if ($price && $discount > 0) {
                                                $discounted = $price - ($price * ($discount / 100));
                                                $set('discounted_price', round($discounted, 2));
                                            } elseif ($discount == 0) {
                                                $set('discounted_price', null);
                                            }
                                        }),

                                    TextInput::make('discounted_price')
                                        ->label('Price After Discount')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('AED')
                                        ->nullable()
                                        ->helperText('Calculated automatically (optional override)'),
                                    Checkbox::make('buy_3_get_1_free')
                                    ->label('Buy 3, Get 1 Free Offer')
                                    ->default(false),
                                    
                                    Checkbox::make('is_set_of_4')
                                    ->label('Set of 4 Tyres')
                                    ->default(false)
                                    ->helperText('Check if this is sold as a set of 4 tyres'),


                            ]),


                                Tab::make('Brand & Origin')->icon('heroicon-o-flag')->schema([
                                    Grid::make(2)->schema([
                                        Select::make('tyre_brand_id')
                                            ->label('Tyre Brand')
                                            ->relationship('tyreBrand', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                    ])
                                ])->columnSpanFull(),
                                
                                Tab::make('SEO')
                                    ->icon('heroicon-o-magnifying-glass-circle')
                                    ->schema([
                                        TextInput::make('meta_title')
                                            ->label('Meta Title')
                                            ->maxLength(255),

                                        Textarea::make('meta_description')
                                            ->label('Meta Description')
                                            ->maxLength(500),

                                        TextInput::make('alt_text')
                                            ->label('Alt Text')
                                            ->maxLength(255),
                                    ]),
                            ])->columnSpanFull(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Components\Section::make()->schema([
                Components\Split::make([
                    Components\Grid::make(2)->schema([
                        Components\Group::make([
                            Components\TextEntry::make('sku')
                                ->badge()
                                ->color('info')
                                ->label('SKU'),
                            Components\TextEntry::make('title')
                                ->label('Title'),
                            Components\TextEntry::make('tyreBrand.name')
                                ->label('Brand'),
                            // Components\TextEntry::make('tire_size')
                            //     ->label('Tire Size'),
                            Components\TextEntry::make('price_vat_inclusive')
                                ->money('AED')
                                ->label('Price (VAT Inclusive)'),
                        ]),
                        Components\Group::make([
                            Components\TextEntry::make('model')
                                ->label('Model')
                                ->placeholder('No model specified'),
                            Components\TextEntry::make('wheel_diameter')
                                ->label('Wheel Diameter')
                                ->formatStateUsing(fn ($state) => $state ? (fmod($state, 1) == 0 ? (int)$state . '"' : $state . '"') : $state)
                                ->placeholder('No diameter specified'),
                            Components\TextEntry::make('load_index')
                                ->label('Load Index'),
                            Components\TextEntry::make('speed_rating')
                                ->label('Speed Rating'),
                            Components\TextEntry::make('production_year')
                                ->label('Production Year'),
                            Components\TextEntry::make('created_at')
                                ->label('Created At')
                                ->dateTime('D, M j, Y \a\t g:i A'),
                        ]),
                    ]),
                    Components\ImageEntry::make('image')
                        ->simpleLightbox()
                        ->hiddenLabel()
                        ->grow(false)
                        ->width(200)
                        ->height(150)
                        ->defaultImageUrl(url('/images/placeholder.png')),
                ])->from('xl'),
            ]),

            Section::make('Description')
                ->schema([
                    TextEntry::make('description')
                        ->placeholder('No description')
                        ->prose()
                        ->markdown()
                        ->hiddenLabel(),
                ])->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->headerActions([
                ImportAction::make()
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('danger')
                    ->importer(TyreImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Tyres')
                    ->modalDescription('Are you sure you want to delete all tyres? This action cannot be undone.')
                    ->action(fn () => Tyre::query()->delete()),
            ])

            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                    ->label('Feature Image')
                    ->collection('tyre_feature_image')
                    ->circular()
                    ->simpleLightbox()
                    ->toggleable(true, false)
                    ->height(40)
                    ->defaultImageUrl('/images/placeholder-tyre.png'),


                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tyreBrand.name')
                    ->label(__('Brand'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->label(__('SKU'))
                    ->searchable()
                    ->sortable(),

                // Tables\Columns\TextColumn::make('tire_size')
                //     ->label(__('Tire Size'))
                //     ->searchable()
                //     ->sortable(),

                Tables\Columns\TextColumn::make('price_vat_inclusive')
                    ->label(__('Price'))
                    ->money('AED') // Adjust currency as needed
                    ->sortable(),

                Tables\Columns\TextColumn::make('discounted_price')
                    ->label(__('Discounted Price'))
                    ->money('AED')
                    ->sortable(),

                Tables\Columns\TextColumn::make('width')
                    ->label(__('Width')),

                Tables\Columns\TextColumn::make('height')
                    ->label(__('Height')),

                Tables\Columns\TextColumn::make('wheel_diameter')
                    ->label(__('Diameter'))
                    ->formatStateUsing(fn ($state) => $state ? (fmod($state, 1) == 0 ? (int)$state : $state) : $state),

                Tables\Columns\TextColumn::make('tyreModel.name')
                    ->label(__('Model'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('tyreSize.size')
                    ->label('Size'),

                Tables\Columns\TextColumn::make('tyreCountry.name')
                    ->label('Country'),

                Tables\Columns\TextColumn::make('load_index')
                    ->label(__('Load Index')),

                Tables\Columns\TextColumn::make('speed_rating')
                    ->label(__('Speed Rating')),

                Tables\Columns\TextColumn::make('weight_kg')
                    ->label(__('Weight (kg)')),

                Tables\Columns\TextColumn::make('production_year')
                    ->label(__('Year'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('warranty')
                    ->label('Warranty'),

                Tables\Columns\IconColumn::make('buy_3_get_1_free')
                    ->label('Buy 3 Get 1')
                    ->boolean(),
                    
                Tables\Columns\IconColumn::make('is_set_of_4')
                    ->label('Set of 4')
                    ->boolean(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('slug')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('Slug'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                DateFilter::make('created_at')
                    ->columnSpanFull()
                    ->label(__('Creation date')),
            ], Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(4)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTyres::route('/'),
            'create' => Pages\CreateTyre::route('/create'),
            'edit' => Pages\EditTyre::route('/{record}/edit'),
            'view' => Pages\ViewTyre::route('/{record}'),
        ];
    }
}
