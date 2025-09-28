<?php

namespace App\Filament\Resources;

use App\Filament\Imports\AutoPartImporter;
use App\Filament\Imports\BatteryImporter;
use App\Filament\Resources\BatteryResource\Pages;
use App\Models\Battery;
use App\Models\Brand;
use App\Models\Category;
use Closure;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;

class BatteryResource extends Resource
{
    protected static ?string $model = Battery::class;

    protected static ?string $navigationIcon = 'heroicon-o-battery-50';



    protected static ?string $navigationGroup = 'Batteries';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationLabel = 'Batteries';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Battery Entry')
                    ->tabs([

                        // --- GENERAL INFO TAB ---
                        Tab::make(__('General Info'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make(__('Basic Information'))->schema([
                                    Grid::make(2)->schema([
                                        TextInput::make('name')
                                            ->label('Name')
                                            ->required()
                                            ->maxLength(150)
                                            ->reactive()
                                            ->afterStateUpdated(fn (?string $state, Set $set) =>
                                                $set('slug', \Illuminate\Support\Str::slug($state))
                                            ),


                                        TextInput::make('slug')
                                            ->label('URL Slug')
                                            ->disabled()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true),
                                    ]),

                                    Grid::make(3)->schema([
                                        TextInput::make('item_code')
                                            ->label('Item Code')
                                            ->unique(ignoreRecord: true),
                                        TextInput::make('sku')
                                            ->label('SKU')
                                            ->required()
                                            ->unique(ignoreRecord: true),
                                        // SelectTree::make('category_id')
                                        //     ->placeholder('Select Category')
                                        //     ->label('Category')
                                        //     ->required()
                                        //     ->searchable()
                                        //     ->enableBranchNode()
                                        //     ->relationship('category', 'name', 'parent_id'),
                                    ]),
                                ])->columns(1),

                                Section::make(__('Origin'))->schema([
                                    Grid::make(1)->schema([
                                        // Select::make('brand_id')
                                        //     ->label('Brand')
                                        //     ->relationship('brand', 'name')
                                        //     ->searchable()
                                        //     ->preload()
                                        //     ->nullable()
                                        //     ->createOptionForm([
                                        //         Tabs::make('BrandTabs')
                                        //             ->tabs([
                                        //                 Tab::make('Basic Info')
                                        //                     ->icon('heroicon-o-information-circle')
                                        //                     ->schema([
                                        //                         Grid::make()
                                        //                             ->schema([
                                        //                                 TextInput::make('name')
                                        //                                     ->label('Name')
                                        //                                     ->required()
                                        //                                     ->maxLength(150)
                                        //                                     ->live(onBlur: true)
                                        //                                     ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                        //                                         if ($get('name')) {
                                        //                                             $set('slug', SlugService::createSlug(Brand::class, 'slug', $state));
                                        //                                         }
                                        //                                     }),

                                        //                                 TextInput::make('slug')
                                        //                                     ->label('URL Slug')
                                        //                                     ->readOnly()
                                        //                                     ->maxLength(255)
                                        //                                     ->unique(ignoreRecord: true),

                                        //                                 TextInput::make('website_url')
                                        //                                     ->label('Website')
                                        //                                     ->url()
                                        //                                     ->maxLength(255),
                                        //                             ])
                                        //                             ->columns(1),

                                        //                         Textarea::make('description')
                                        //                             ->label('Description')
                                        //                             ->columnSpanFull(),

                                        //                         SpatieMediaLibraryFileUpload::make('logo')
                                        //                             ->label('Logo')
                                        //                             ->collection('logo')
                                        //                             ->image()
                                        //                             ->imageEditor()
                                        //                             ->columnSpanFull(),

                                        //                         Checkbox::make('is_active')
                                        //                             ->label('Is Active?')
                                        //                             ->default(true),

                                        //                         Checkbox::make('is_featured')
                                        //                             ->label('Is Featured?'),
                                        //                     ]),

                                        //                 Tab::make('SEO')
                                        //                     ->icon('heroicon-o-magnifying-glass')
                                        //                     ->schema([
                                        //                         TextInput::make('meta_title')
                                        //                             ->label('Meta Title')
                                        //                             ->maxLength(255)
                                        //                             ->columnSpanFull(),

                                        //                         Textarea::make('meta_description')
                                        //                             ->label('Meta Description')
                                        //                             ->maxLength(500)
                                        //                             ->columnSpanFull(),

                                        //                         TextInput::make('meta_keywords')
                                        //                             ->label('Meta Keywords')
                                        //                             ->columnSpanFull(),
                                        //                     ]),
                                        //             ])
                                        //             ->columnSpanFull(),
                                        //     ]),
                                        Select::make('battery_country_id')
                                            ->label('Battery Country')
                                            ->relationship('batteryCountry', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->nullable()
                                            ->createOptionForm([
                                                TextInput::make('name')->label('Country Name')->required(),
                                                TextInput::make('code')->label('Country Code')->nullable(),
                                            ]),

                                    ])
                                ])->collapsible(),
                            ]),

                        // --- SPECIFICATIONS TAB ---
                        Tab::make(__('Specifications'))
                            ->icon('heroicon-o-cog')
                            ->schema([
                                TextInput::make('warranty')->required(),
                                Select::make('battery_brand_id')
                                    ->label('Battery Brand')
                                    ->relationship('batteryBrand', 'value')
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->createOptionForm([
                                        TextInput::make('value')->label('Brand Name')->required(),
                                    ]),

                                Select::make('capacity_id')
                                    ->label('Capacity')
                                    ->relationship('capacity', 'value')
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->createOptionForm([
                                        TextInput::make('value')->label('Capacity')->required(),
                                    ]),

                                Select::make('dimension_id')
                                    ->label('Dimensions')
                                    ->relationship('dimension', 'value')
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->createOptionForm([
                                        TextInput::make('value')->label('Dimension')->required(),
                                    ]),
                                TextInput::make('weight')
                                    ->label('Weight (KG)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->nullable(),


                                Select::make('attributes')
                                    ->label('Battery Attributes')
                                    ->multiple()
                                    ->relationship('attributes', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->columnSpanFull(),
 
                            ])->columns(2),

                        // --- PRICING TAB ---
                    Tab::make(__('Pricing'))
                        ->icon('heroicon-o-currency-dollar')
                        ->schema([
                            Grid::make(3)->schema([
                                TextInput::make('regular_price')
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
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('%')
                                    ->nullable()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $price = floatval($get('regular_price'));
                                        $discount = floatval($state);

                                        if ($price && $discount > 0) {
                                            $discounted = $price - ($price * ($discount / 100));
                                            $set('discounted_price', round($discounted, 2));
                                        } elseif ($discount == 0) {
                                            $set('discounted_price', null);
                                        }
                                    }),

                                TextInput::make('discounted_price')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('AED')
                                    ->nullable()
                                    ->helperText('Calculated automatically when you set a discount (you can override manually)'),
                            ])
                        ]),


                        // --- MEDIA TAB ---
                        Tab::make(__('Media'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('feature_image')
                                    ->label(__('Feature Image'))
                                    ->collection('battery_feature_image')
                                    ->image()
                                    ->maxSize(5120),
                                
                                SpatieMediaLibraryFileUpload::make('battery_secondary_image')
                                    ->label(__('Secondary Image'))
                                    ->collection('battery_secondary_image')
                                    ->image()
                                    ->maxSize(5120),

                                SpatieMediaLibraryFileUpload::make('battery_gallery')
                                    ->label(__('Gallery'))
                                    ->collection('battery_gallery')
                                    ->multiple()
                                    ->maxFiles(20)
                                    ->acceptedFileTypes([
                                        'video/mp4', 'image/jpeg', 'image/png', 'image/webp',
                                        'image/gif', 'image/jpg'
                                    ])
                                    ->imageEditor()
                                    ->reorderable(),
                            ]),

                        // --- DETAILS TAB ---
                        Tab::make(__('Details'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Textarea::make('description')
                                    ->label('Detailed Description')
                                    ->rows(6)
                                    ->maxLength(65535),
                            ]),
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
    
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(__('id')),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                    ->label('Image')
                    ->collection('battery_feature_image')
                    ->circular()
                    ->height(40)
                    ->defaultImageUrl('/images/placeholder-battery.png'),

                TextColumn::make('name')
                    ->label('Battery Name')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),

                TextColumn::make('batteryBrand.value')
                    ->label('Brand')
                    ->searchable(),

                TextColumn::make('capacity.value')
                    ->label('Capacity'),

                TextColumn::make('dimension.value')
                    ->label('Dimension'),

                TextColumn::make('batteryCountry.name')
                    ->label('Country'),

                TextColumn::make('weight')
                    ->label('Weight (KG)'),

                TextColumn::make('warranty')
                    ->label('Warranty'),

                // Tables\Columns\TextColumn::make('category.parent.name')
                //     ->label('Parent Category')
                //     ->searchable()
                //     ->sortable(),


                // Tables\Columns\TextColumn::make('category.name')
                //     ->label('Sub Category')
                //     ->searchable()
                //     ->sortable(),

                TextColumn::make('regular_price')
                    ->label('Price (AED)')
                    ->money('aed', true)
                    ->sortable(),

                TextColumn::make('discount_percentage')
                    ->label('Discount %')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? $state . '%' : '-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('discounted_price')
                    ->label('Final Price')
                    ->money('aed', true)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Import Batteries')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('danger')
                    ->importer(BatteryImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Batteries')
                    ->modalDescription('Are you sure you want to delete all batteries? This action cannot be undone.')
                    ->action(fn () => Battery::query()->delete()),
            ])

            ->filters([
                // SelectFilter::make('brand_id')
                //     ->label('Brand')
                //     ->relationship('brand', 'name')
                //     ->searchable(),

//                Filter::make('category')
//                    ->columnSpanFull()
//                    ->form([
//                        SelectTree::make('category_id')
//                            ->placeholder('Search for a Category...')
//                            ->enableBranchNode()
//                            ->hiddenLabel()
//                            ->relationship('category', 'name', 'parent_id')
//                            ->searchable(),
//                    ])
//                    ->query(function (Builder $query, array $data) {
//                        if (!empty($data['category_id'])) {
//                            $selectedCategoryId = $data['category_id'];
//
//                            // Check if the selected category has children
//                            $hasChildren = Category::where('parent_id', $selectedCategoryId)->exists();
//
//                            if ($hasChildren) {
//                                // If it's a parent category, get all its descendants
//                                $categoryIds = self::getCategoryWithDescendants($selectedCategoryId);
//                                $query->whereIn('category_id', $categoryIds);
//                            } else {
//                                // If it's a child category, just filter for that category
//                                $query->where('category_id', $selectedCategoryId);
//                            }
//                        }
//                    })
//                    ->indicateUsing(function (array $data): ?string {
//                        if (empty($data['category_id'])) {
//                            return null;
//                        }
//
//                        $category = Category::find($data['category_id']);
//                        $categoryName = $category->name ?? 'Unknown Category';
//
//                        // Check if it's a parent category
//                        $isParent = Category::where('parent_id', $data['category_id'])->exists();
//
//                        if ($isParent) {
//                            return "Showing blogs in category and subcategories: {$categoryName}";
//                        }
//
//                        return "Showing blogs in category: {$categoryName}";
//                    }),
            ], Tables\Enums\FiltersLayout::Modal)
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected static function getCategoryWithDescendants($categoryId): \Illuminate\Support\Collection
    {
        $categoryIds = collect([$categoryId]);

        $getDescendantIds = function ($parentId) use (&$getDescendantIds) {
            return Category::where('parent_id', $parentId)
                ->pluck('id')
                ->flatMap(function ($id) use ($getDescendantIds) {
                    return collect([$id])->merge($getDescendantIds($id));
                });
        };

        return $categoryIds->merge($getDescendantIds($categoryId));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBatteries::route('/'),
            'create' => Pages\CreateBattery::route('/create'),
            'edit' => Pages\EditBattery::route('/{record}/edit'),
        ];
    }
}
