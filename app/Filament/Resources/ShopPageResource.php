<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopPageResource\Pages\EditShopPage;
use App\Filament\Resources\ShopPageResource\Pages\ListShopPages;
use App\Models\ShopPage;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;

class ShopPageResource extends Resource
{
    protected static ?string $model = ShopPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Shop Page Sections')
                    ->tabs([
                        Tabs\Tab::make('Section 1')
                            ->schema([
                                Section::make('Section 1 Content')
                                    ->schema([
                                        TextInput::make('section_1_title')->label('Title'),
                                        MarkdownEditor::make('section_1_content')->label('Text'),
                                    ])->columns(1),
                            ]),

                        Tabs\Tab::make('Section 2')
                            ->schema([
                                Section::make('Section 2 Content')
                                    ->schema([
                                        TextInput::make('section_2_title')->label('Title'),
                                        MarkdownEditor::make('section_2_content')->label('Text'),
                                        FileUpload::make('section_2_image')
                                            ->label('Image')
                                            ->directory('shop-page/section-2')
                                            ->image()
                                    ])->columns(1),
                            ]),
                        Tabs\Tab::make('SEO')
                                ->icon('heroicon-o-magnifying-glass-circle')
                                ->schema([
                                    TextInput::make('meta_title')
                                        ->label('Meta Title')
                                        ->maxLength(255),

                                    \Filament\Forms\Components\Textarea::make('meta_description')
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
                TextColumn::make('id'),
                TextColumn::make('updated_at')->dateTime('d M Y H:i'),
            ])
            ->defaultSort('id');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShopPages::route('/'),
            'edit' => EditShopPage::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
    public static function canDeleteAny(): bool
    {
        return false;
    }

}
