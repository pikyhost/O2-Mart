<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttributeResource\Pages;
use App\Models\Attribute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

// class AttributeResource extends Resource
// {
//     protected static ?string $model = Attribute::class;

//     protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
//                 Forms\Components\Section::make()
//                     ->schema([
//                         Forms\Components\TextInput::make('name')
//                             ->required()
//                             ->maxLength(255)
//                             ->live(onBlur: true)
//                             ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

//                         Forms\Components\TextInput::make('slug')
//                             ->required()
//                             ->maxLength(255)
//                             ->unique(ignoreRecord: true),

//                         Forms\Components\Select::make('type')
//                             ->options([
//                                 'text' => 'Text',
//                                 'number' => 'Number',
//                                 'select' => 'Select',
//                                 'multiselect' => 'Multiselect',
//                                 'boolean' => 'Boolean',
//                                 'date' => 'Date',
//                                 'range' => 'Range',
//                             ])
//                             ->required()
//                             ->live(),

//                         Forms\Components\Textarea::make('description')
//                             ->columnSpanFull(),

//                         Forms\Components\KeyValue::make('options')
//                             ->visible(fn (Forms\Get $get) => in_array($get('type'), ['select', 'multiselect'])),

//                                 Forms\Components\TextInput::make('unit')
//                                     ->maxLength(50),

//                                 Forms\Components\Toggle::make('is_required')
//                                     ->default(false),

//                                 Forms\Components\Toggle::make('is_filterable')
//                                     ->default(true),

//                                 Forms\Components\Toggle::make('is_searchable')
//                                     ->default(false),

//                                 Forms\Components\TextInput::make('sort_order')
//                                     ->numeric()
//                                     ->default(0),

//                                 Forms\Components\Toggle::make('is_active')
//                                     ->default(true),
//                     ])->columns(2)
//             ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 Tables\Columns\TextColumn::make('name')
//                     ->searchable()
//                     ->sortable(),

//                 Tables\Columns\TextColumn::make('type')
//                     ->badge(),

//                 Tables\Columns\TextColumn::make('unit'),

//                 Tables\Columns\IconColumn::make('is_filterable')
//                     ->boolean(),

//                 Tables\Columns\IconColumn::make('is_active')
//                     ->boolean(),

//                 Tables\Columns\TextColumn::make('created_at')
//                     ->dateTime()
//                     ->sortable()
//                     ->toggleable(isToggledHiddenByDefault: true),
//             ])
//             ->filters([
//                 Tables\Filters\SelectFilter::make('type')
//                     ->options([
//                         'text' => 'Text',
//                         'number' => 'Number',
//                         'select' => 'Select',
//                         'multiselect' => 'Multiselect',
//                         'boolean' => 'Boolean',
//                         'date' => 'Date',
//                         'range' => 'Range',
//                     ]),

//                 Tables\Filters\TernaryFilter::make('is_active')
//                     ->label('Active'),
//             ])
//             ->actions([
//                 Tables\Actions\EditAction::make(),
//                 Tables\Actions\DeleteAction::make(),
//             ])
//             ->bulkActions([
//                 Tables\Actions\BulkActionGroup::make([
//                     Tables\Actions\DeleteBulkAction::make(),
//                 ]),
//             ]);
//     }

//     public static function getPages(): array
//     {
//         return [
//             'index' => Pages\ListAttributes::route('/'),
//             'create' => Pages\CreateAttribute::route('/create'),
//             'edit' => Pages\EditAttribute::route('/{record}/edit'),
//         ];
//     }
// }
