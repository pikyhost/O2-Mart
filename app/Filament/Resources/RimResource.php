<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RimResource\Pages;
use App\Filament\Imports\RimImporter;
use App\Models\Rim;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\ImportAction;

class RimResource extends BaseResource
{
    protected static ?string $model = Rim::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'Rims';
    protected static ?int $navigationSort = 17;
    protected static ?string $navigationLabel = 'Rims';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Rim Details')
                    ->tabs([
                        Tab::make('General Info')
                            ->schema([
                                TextInput::make('name')
                                        ->label('Product Name')
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set) =>
                                            $set('slug', \Illuminate\Support\Str::slug($state))
                                        ),
                                TextInput::make('slug')
                                        ->label('Slug')
                                        ->disabled()
                                        ->unique(ignoreRecord: true)
                                        ->maxLength(255),

                                MarkdownEditor::make('description')->label('Product Full Description'),
                                Select::make('rim_brand_id')
                                        ->label('Brand')
                                        ->relationship('rimBrand', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required(),
                            ]),

                        Tab::make('Specifications')
                            ->schema([
                                TextInput::make('colour')->label('Colour'),
                                TextInput::make('condition')->label('Condition'),
                                TextInput::make('specification')->label('Specification'),
                                TextInput::make('bolt_pattern')->label('Bolt Pattern'),
                                Select::make('rim_size_id')
                                        ->label('RIM Size')
                                        ->relationship('rimSize', 'size')
                                        ->searchable()
                                        ->preload(),
                                TextInput::make('offsets')->label('Offsets'),
                                TextInput::make('centre_caps')->label('Centre Caps'),
                                Select::make('rim_attribute_ids')
                                    ->label('Wheel Attributes')
                                    ->multiple()
                                    ->options(fn () => \App\Models\RimAttribute::pluck('name', 'id'))
                                    ->preload()
                                    ->searchable()
                                    ->relationship('attributes', 'name'),


                                Toggle::make('is_set_of_4')->label('Set of 4'),
                            ]),

                        Tab::make('Identification')
                            ->schema([
                                TextInput::make('item_code')->label('Item Code'),
                                TextInput::make('sku')->label('SKU')->nullable(),
                                TextInput::make('warranty')->label('Warranty'),
                            ]),

                        Tab::make('Brand Info')
                            ->schema([
                                Select::make('rim_country_id')
                                        ->label('Country of Origin')
                                        ->relationship('rimCountry', 'name')
                                        ->searchable()
                                        ->preload(),
                            ]),

                        Tab::make('Price')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('regular_price')
                                        ->label('Regular Price')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('AED')
                                        ->nullable()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                            $price = floatval($state);
                                            $discount = floatval($get('discount_percent'));

                                            if ($price && $discount > 0) {
                                                $discounted = $price - ($price * ($discount / 100));
                                                $set('discounted_price', round($discounted, 2));
                                            }
                                        }),

                                    TextInput::make('discount_percent')
                                        ->label('Discount %')
                                        ->numeric()
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
                                        ->label('Discounted Price')
                                        ->numeric()
                                        ->step(0.01)
                                        ->prefix('AED')
                                        ->nullable()
                                        ->helperText('Calculated automatically (optional override)'),
                                ]),
                            ]),


                        Tab::make('Weight')
                            ->schema([
                                TextInput::make('weight')->label('Weight (KG) - set of 4')->numeric(),
                            ]),

                        Tab::make('Media')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('product_image')
                                    ->label('Product Image')
                                    ->collection('rim_feature_image')
                                    ->image()
                                    ->maxSize(5120),
                            ]),
                        Tab::make('SEO')
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
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(__('id')),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                    ->toggleable(true, false)
                    ->circular()
                    ->simpleLightbox()
                    ->collection('rim_feature_image')
                    ->label(__('Feature Image'))
                    ->defaultImageUrl('/images/placeholder-rim.png'),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('Product Name'))
                    ->searchable(),

                       Tables\Columns\TextColumn::make('description')
                    ->label(__('Product Name'))
                    ->html()
                    ->searchable(),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),

                Tables\Columns\TextColumn::make('rimBrand.name')
                    ->label('Brand')
                    ->searchable(),

                Tables\Columns\TextColumn::make('rimSize.size')
                    ->label('Size')
                    ->searchable(),

                Tables\Columns\TextColumn::make('regular_price')
                    ->label('Price')
                    ->money('AED'),

                Tables\Columns\TextColumn::make('weight')
                    ->label('Weight (KG)'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(50)
                    ,

                Tables\Columns\TextColumn::make('slug')
                    
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
            ->headerActions([
                ImportAction::make()
                    ->label('Bulk Upload')
                    ->importer(RimImporter::class)
                    ->chunkSize(10),
                Tables\Actions\Action::make('deleteAll')
                    ->label('Delete All Records')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete All Rims')
                    ->modalDescription('Are you sure you want to delete all rims? This action cannot be undone.')
                    ->action(fn () => Rim::query()->delete()),
            ])
            ->filters([
                DateFilter::make('created_at')
                    ->columnSpanFull()
                    ->label(__('Creation date')),
            ], Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(4)
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListRims::route('/'),
            'create' => Pages\CreateRim::route('/create'),
            'edit' => Pages\EditRim::route('/{record}/edit'),
        ];
    }
}
