<?php

namespace App\Filament\Resources;

use App\Exports\AutoPartCountryExporter;
use App\Filament\Imports\AutoPartImporter;
use App\Filament\Resources\AutoPartResource\Pages;
use App\Models\AutoPart;
use App\Models\Category;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;


class AutoPartResource extends Resource
{
    protected static ?string $model = AutoPart::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Auto Parts';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('AutoPart Tabs')
                    ->tabs([
                        Tab::make(__('General Info'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Product Name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) =>
                                        $set('slug', Str::slug($state))
                                    ),

                                TextInput::make('slug')
                                    ->unique(ignoreRecord: true)
                                    ->label(__('Slug'))
                                    ->maxLength(255)
                                    ->disabled(), 

                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(100),

                                SelectTree::make('category_id')
                                    ->placeholder('Select Category')
                                    ->label('Category')
                                    ->required()
                                    ->searchable()
                                    ->enableBranchNode()
                                    ->relationship('category', 'name', 'parent_id'),


//                                Select::make('parent_category_id')
//                                    ->label('Parent Category')
//                                    ->options(fn () => Category::whereNull('parent_id')->pluck('name', 'id'))
//                                    ->searchable()
//                                    ->required()
//                                    ->live(),
//
//                                Select::make('sub_category_id')
//                                    ->label('Sub Category')
//                                    ->options(function (callable $get) {
//                                        $parentId = $get('parent_category_id');
//                                        return Category::where('parent_id', $parentId)->pluck('name', 'id');
//                                    })
//                                    ->searchable()
//                                    ->required()
//                                    ->disabled(fn (callable $get) => !$get('parent_category_id')),
                                Select::make('auto_part_brand_id')
                                    ->label('AutoPart Brand')
                                    ->relationship('autoPartBrand', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                                Select::make('auto_part_country_id')
                                    ->label('AutoPart Country')
                                    ->relationship('autoPartCountry', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                                Select::make('viscosity_grade_id')
                                    ->label('Viscosity Grade')
                                    ->relationship('viscosityGrade', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                            ]),

                        Tab::make('Pricing & Dimensions')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('price_including_vat')
                                        ->label('Price Including VAT')
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
                                            $price = floatval($get('price_including_vat'));
                                            $discount = floatval($state);

                                            if ($price && $discount > 0) {
                                                $discounted = $price - ($price * ($discount / 100));
                                                $set('discounted_price', round($discounted, 2));
                                            } elseif ($discount == 0) {
                                                $set('discounted_price', null);
                                            }
                                        }),

                                    TextInput::make('discounted_price')
                                        ->label('Discounted Price')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('AED')
                                        ->nullable()
                                        ->helperText('Calculated automatically (optional override)'),
                                ]),

                                Section::make('Dimensions & Weight')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            TextInput::make('length')
                                                ->label('Length (cm)')
                                                ->numeric()
                                                ->step(0.01)
                                                ->nullable(),
                                            
                                            TextInput::make('width')
                                                ->label('Width (cm)')
                                                ->numeric()
                                                ->step(0.01)
                                                ->nullable(),
                                            
                                            TextInput::make('height')
                                                ->label('Height (cm)')
                                                ->numeric()
                                                ->step(0.01)
                                                ->nullable(),
                                            
                                            TextInput::make('weight')
                                                ->label('Weight (kg)')
                                                ->numeric()
                                                ->step(0.01)
                                                ->nullable(),
                                        ]),
                                    ]),
                            ]),      
                        // Tab::make('Compatibility')
                        //     ->icon('heroicon-o-link')
                        //     ->schema([
                        //         Repeater::make('compatibleVehicles')
                        //             ->label('Compatible Vehicles')
                        //             ->dehydrated(false) 
                        //             ->schema([
                        //                 Select::make('car_make_id')
                        //                     ->label('Make')
                        //                     ->options(\App\Models\CarMake::pluck('name', 'id'))
                        //                     ->required()
                        //                     ->reactive(),

                        //                 Select::make('car_model_id')
                        //                     ->label('Model')
                        //                     ->options(fn (callable $get) =>
                        //                         \App\Models\CarModel::where('car_make_id', $get('car_make_id'))->pluck('name', 'id'))
                        //                     ->required()
                        //                     ->reactive()
                        //                     ->afterStateUpdated(function ($state, callable $set) {
                        //                         if ($state) {
                        //                             $model = \App\Models\CarModel::find($state);
                        //                             if ($model && $model->year_from) {
                        //                                 $yearFrom = $model->year_from;
                        //                                 $yearTo = $model->year_to ?? $yearFrom;
                        //                                 $years = collect(range($yearFrom, $yearTo))->mapWithKeys(fn ($y) => [$y => $y])->toArray();
                        //                                 $set('year_options', $years);
                        //                             }
                        //                         }
                        //                     }),

                        //                 Select::make('year')
                        //                     ->label('Year')
                        //                     ->options(fn (callable $get) => $get('year_options') ?? [])
                        //                     ->required(),
                        //             ])
                        //             ->createItemButtonLabel('Add Compatibility')
                        //             ->columns(2),
                        //             ]),
                        Tab::make(__('Media'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                               Section::make('Primary Image')->schema([
                                   FileUpload::make('photo_link')
                                       ->label(__('Feature Image'))
                                       ->image()
                                       ->avatar()
                                       ->maxSize(5120)
                                       ->directory('auto-parts')
                                       ->nullable(),

                                   TextInput::make('photo_alt_text')
                                       ->label(__('Feature Image Alt Text'))
                                       ->required()
                                       ->maxLength(255),
                               ]),

                                SpatieMediaLibraryFileUpload::make('auto_part_secondary_image')
                                    ->label(__('Secondary Image'))
                                    ->collection('auto_part_secondary_image')
                                    ->image()
                                    ->maxSize(5120),

                                SpatieMediaLibraryFileUpload::make('auto_part_gallery')
                                    ->maxFiles(20)
                                    ->label(__('Gallery'))
                                    ->collection('auto_part_gallery')
                                    ->multiple()
                                    ->acceptedFileTypes(['video/mp4', 'video/mpeg', 'video/quicktime',
                                        'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/jpg'])
                                    ->imageEditor()
                                    ->reorderable(),
                            ]),

                        Tab::make('Details')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                MarkdownEditor::make('description')
                                    ->label('Description')
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                MarkdownEditor::make('details')
                                    ->label('Details')
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
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
                        ]),
   
                    ])
                    ->columnSpanFull(),
            ]);
    }

    // public static function mutateFormDataBeforeCreate(array $data): array
    // {
    //     session()->put('compatibleVehicles', $data['compatibleVehicles'] ?? []);
    //     // unset($data['compatibleVehicles']); 

    //     return $data;
    // }

    // public static function afterCreate(\Illuminate\Database\Eloquent\Model $record): void
    // {
    //     $compatibilities = session()->pull('compatibleVehicles', []);

    //     foreach ($compatibilities as $item) {
    //         if (!empty($item['car_model_id']) && !empty($item['year'])) {
    //             $record->compatibleVehicles()->attach($item['car_model_id'], [
    //                 'year_from' => $item['year'],
    //                 'year_to' => $item['year'],
    //                 'is_verified' => true,
    //             ]);
    //         }
    //     }
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(__('id')),

                Tables\Columns\ImageColumn::make('photo_link')
                    ->toggleable(true, false)
                    ->circular()
                    ->simpleLightbox()
                    ->placeholder('-')
                    ->label(__('Feature Image')),

                // Basic Info Columns
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('category.parent.name')
                    ->label('Parent Category')
                    ->searchable()
                    ->sortable(),


                Tables\Columns\TextColumn::make('category.name')
                    ->label('Sub Category')
                    ->searchable()
                    ->sortable(),

                // Pricing Columns
                Tables\Columns\TextColumn::make('price_including_vat')
                    ->label('Price')
                    ->money('AED') // Adjust currency as needed
                    ->sortable()
                    ->toggleable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('discount_percentage')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Discount %')
                    ->suffix('%')
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('discounted_price')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Discounted')
                    ->money('AED') // Adjust currency as needed
                    ->sortable()
                    ->alignEnd(),

                // Dimensions Columns
                Tables\Columns\TextColumn::make('length')
                    ->label('Length (cm)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                   
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('width')
                    ->label('Width (cm)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                  
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('height')
                    ->label('Height (cm)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                   
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('weight')
                    ->label('Weight (kg)')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                   
                    ->alignEnd(),

                // Additional Info
                Tables\Columns\TextColumn::make('viscosity_grade')
                    ->label('Viscosity')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('viscosityGrade.name')
                    ->label('Viscosity')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('autoPartBrand.name')
                    ->label('Brand')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('autoPartCountry.name')
                    ->label('Country')
                    ->toggleable(isToggledHiddenByDefault: true),


                // Timestamps
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                DateFilter::make('created_at')
                    ->columnSpanFull()
                    ->label(__('Creation date')),
                Tables\Filters\SelectFilter::make('autoPartBrand')
                    ->relationship('autoPartBrand', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('autoPartCountry')
                    ->relationship('autoPartCountry', 'name')
                    ->searchable()
                    ->preload(),

                
                Tables\Filters\SelectFilter::make('viscosityGrade')
                    ->relationship('viscosityGrade', 'name')
                    ->searchable()
                    ->preload(),


                Tables\Filters\TernaryFilter::make('has_discount')
                    ->label('Discounted Items')
                    ->nullable()
                    ->queries(
                        true: fn ($query) => $query->where('discount_percentage', '>', 0),
                        false: fn ($query) => $query->where('discount_percentage', 0),
                    ),
//
//                SelectTree::make('category_id')
//                    ->placeholder('Select Category')
//                    ->label('Category')
//                    ->searchable()
//                    ->enableBranchNode()
//                    ->relationship('category', 'name', 'parent_id'),

//                SelectFilter::make('parent_category_id')
//                    ->label('Parent Category')
//                    ->options(fn () => Category::whereNull('parent_id')->pluck('name', 'id'))
//                    ->searchable()
//                    ->placeholder('All Parent Categories'),
//
//                SelectFilter::make('sub_category_id')
//                    ->label('Sub Category')
//                    ->options(fn () => Category::whereNotNull('parent_id')->pluck('name', 'id'))
//                    ->searchable()
//                    ->placeholder('All Sub Categories'),
            ], Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('danger')
                    ->importer(AutoPartImporter::class),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Auto Parts')
                    ->modalDescription('Are you sure you want to delete all auto parts? This action cannot be undone.')
                    ->action(fn () => AutoPart::query()->delete()),
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
            'index' => Pages\ListAutoParts::route('/'),
            'create' => Pages\CreateAutoPart::route('/create'),
            'edit' => Pages\EditAutoPart::route('/{record}/edit'),
        ];
    }
public static function mutateFormDataBeforeCreate(array $data): array
{
    $data['discounted_price'] = self::calculateDiscountedPrice($data);
    return $data;
}

public static function mutateFormDataBeforeSave(array $data): array
{
    $data['discounted_price'] = self::calculateDiscountedPrice($data);
    return $data;
}

public static function mutateFormDataBeforeUpdate(array $data): array
{
    $data['discounted_price'] = self::calculateDiscountedPrice($data);
    return $data;
}

protected static function calculateDiscountedPrice(array $data): float
{
    $price = floatval($data['price_including_vat'] ?? 0);
    $discount = floatval($data['discount_percentage'] ?? 0);

    if ($price > 0 && $discount > 0) {
        return round($price - ($price * ($discount / 100)), 2);
    }

    return $price;
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
}
