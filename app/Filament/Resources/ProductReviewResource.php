<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductReviewResource\Pages;
use App\Models\ProductReview;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class ProductReviewResource extends Resource
{
    protected static ?string $model = ProductReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?string $navigationGroup = 'Product Management';
    protected static ?string $navigationLabel = 'Product Reviews';

    public static function form(Form $form): Form
    {
        $classMap = [
            'auto_part' => \App\Models\AutoPart::class,
            'tyre'      => \App\Models\Tyre::class,
            'battery'   => \App\Models\Battery::class,
            'rim'       => \App\Models\Rim::class,
        ];

        $labelColumnMap = [
            'auto_part' => 'name',
            'tyre'      => 'title', 
            'battery'   => 'name',
            'rim'       => 'name',
        ];

    return $form->schema([
        TextInput::make('name')->required()->maxLength(100)->disabledOn('edit'),
        TextInput::make('summary')->required()->maxLength(255)->disabledOn('edit'),
        Textarea::make('review')->required()->rows(5)->disabledOn('edit'),

        Select::make('rating')
            ->options([1=>1,2=>2,3=>3,4=>4,5=>5])
            ->required()
            ->disabledOn('edit'),

        Select::make('product_type_short')
            ->label('Product Type')
            ->options([
                'auto_part' => 'Auto Part',
                'tyre'      => 'Tyre',
                'battery'   => 'Battery',
                'rim'       => 'Rim',
            ])
            ->live()->reactive()
            ->dehydrated(false) // مش هيتخزن؛ هنحوّله في CreateAction
            ->afterStateHydrated(function (Set $set, ?\App\Models\ProductReview $record) use ($classMap) {
                if (!$record) return;
                $short = array_search($record->product_type, $classMap, true);
                if ($short) $set('product_type_short', $short);
            })
            ->disabledOn('edit'),

        Select::make('product_id')
            ->label('Product')
            ->searchable()
            ->preload()
            ->options(function (Get $get) use ($classMap, $labelColumnMap) {
                $short = $get('product_type_short');
                $class = $classMap[$short] ?? null;
                if (!$class) return [];
                $labelCol = $labelColumnMap[$short] ?? 'name';
                return $class::query()->orderBy($labelCol)->pluck($labelCol, 'id');
            })
            ->disabled(fn (Get $get) => blank($get('product_type_short')))
            ->disabledOn('edit'),

        Toggle::make('is_approved')->label('Approved')->default(false),
    ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('summary')->limit(30),
                TextColumn::make('rating')->sortable(),
                TextColumn::make('product_type')
                    ->label('Product Type')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->sortable(),

                TextColumn::make('product_display')
                    ->label('Product')
                    ->getStateUsing(function ($record) {
                        $product = $record->product;
                        return $product ? "{$product->id} - {$product->title}" : 'N/A';
                    }),

                ToggleColumn::make('is_approved')->label('Approved'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListProductReviews::route('/'),
            'edit' => Pages\EditProductReview::route('/{record}/edit'),
        ];
    }
}
