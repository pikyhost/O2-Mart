<?php

namespace App\Filament\Resources;

use App\Filament\Imports\ProductImporter;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TagsColumn;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Product Details')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Info')
                            ->schema([
                                Forms\Components\Select::make('product_type')
                                    ->options([
                                        'auto_parts' => 'Auto Parts',
                                        'batteries' => 'Batteries',
                                        'tyres' => 'Tyres',
                                    ])
                                    ->required()
                                    ->live(),

                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('slug')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                Forms\Components\TextInput::make('sku')
                                    ->label('SKU')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                Forms\Components\TextInput::make('item_code')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Forms\Components\TextInput::make('part_number')
                                    ->label('Part Number')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                Forms\Components\TagsInput::make('cross_reference_numbers')
                                    ->label('Cross Reference Numbers')
                                    ->placeholder('Add alternative part numbers'),

                                Forms\Components\Textarea::make('description')
                                    ->columnSpanFull(),

                                Forms\Components\FileUpload::make('image_url')
                                    ->label('Product Image')
                                    ->image()
                                    ->directory('products'),

                                Forms\Components\TextInput::make('image_alt_text')
                                    ->maxLength(255),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Pricing')
                            ->schema([
                                Forms\Components\TextInput::make('regular_price')
                                    ->numeric()
                                    ->prefix('$'),

                                Forms\Components\TextInput::make('discount_percentage')
                                    ->label('Discount %')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%'),

                                Forms\Components\TextInput::make('discounted_price')
                                    ->label('Discounted Price')
                                    ->numeric()
                                    ->prefix('$'),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Physical Properties')
                            ->schema([
                                Forms\Components\TextInput::make('weight')
                                    ->numeric()
                                    ->suffix('kg'),

                                Forms\Components\TextInput::make('height')
                                    ->numeric()
                                    ->suffix('cm'),

                                Forms\Components\TextInput::make('width')
                                    ->numeric()
                                    ->suffix('cm'),

                                Forms\Components\TextInput::make('length')
                                    ->numeric()
                                    ->suffix('cm'),

                                Forms\Components\TextInput::make('dimensions')
                                    ->maxLength(255),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Categories & Brands')
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('parent_category_name')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('sub_category_name')
                                    ->maxLength(255),

                                Forms\Components\Select::make('brand_id')
                                    ->relationship('brand', 'name')
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('brand_name')
                                    ->maxLength(255),

                                Forms\Components\Select::make('country_id')
                                    ->relationship('country', 'name')
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('country_name')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('model')
                                    ->maxLength(255),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Vehicle Compatibility')
                            ->schema([
                                Forms\Components\Select::make('car_model_id')
                                    ->relationship('carModel', 'name')
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('car_make')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('car_model')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('car_year')
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(now()->year),

                                Forms\Components\TextInput::make('engine_performance')
                                    ->maxLength(255),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Auto Parts')
                            ->schema([
                                Forms\Components\TextInput::make('viscosity_grade')
                                    ->maxLength(255),
                            ])
                            ->visible(fn (Forms\Get $get) => $get('product_type') === 'auto_parts'),

                        Forms\Components\Tabs\Tab::make('Batteries')
                            ->schema([
                                Forms\Components\TextInput::make('warranty')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('capacity')
                                    ->maxLength(255),
                            ])
                            ->visible(fn (Forms\Get $get) => $get('product_type') === 'batteries'),

                        Forms\Components\Tabs\Tab::make('Tyres')
                            ->schema([
                                Forms\Components\TextInput::make('tire_size')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('wheel_diameter')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('load_index')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('speed_rating')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('production_year')
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(now()->year),

                                Forms\Components\TextInput::make('tyre_oem')
                                    ->maxLength(255),
                            ])
                            ->visible(fn (Forms\Get $get) => $get('product_type') === 'tyres'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Image')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('product_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'auto_parts' => 'info',
                        'batteries' => 'success',
                        'tyres' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brand')
                    ->sortable(),

                Tables\Columns\TextColumn::make('regular_price')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('part_number')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TagsColumn::make('cross_reference_numbers')
                    ->label('Cross References')
                    ->separator(','),

                Tables\Columns\TextColumn::make('discounted_price')
                    ->money()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_type')
                    ->options([
                        'auto_parts' => 'Auto Parts',
                        'batteries' => 'Batteries',
                        'tyres' => 'Tyres',
                    ]),

                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('part_number')
                    ->form([
                        Forms\Components\TextInput::make('part_number'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['part_number'], fn ($q) => $q->where('part_number', 'like', '%' . $data['part_number'] . '%'));
                    }),

                Tables\Filters\SelectFilter::make('brand_id')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('danger')
                    ->importer(ProductImporter::class)
            ])
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\CategoriesRelationManager::class,
            RelationManagers\AttributesRelationManager::class,
            RelationManagers\CompatibilityRulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
       public static function canAccess(): bool
    {
        return false;
    }
}
