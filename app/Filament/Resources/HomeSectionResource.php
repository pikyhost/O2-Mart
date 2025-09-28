<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeSectionResource\Pages;
use App\Models\HomeSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;

class HomeSectionResource extends Resource
{
    protected static ?string $model = HomeSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Home Page Sections')
                    ->tabs([
                        Tabs\Tab::make('Section 1')
                            ->schema([
                                Section::make('Section 1 Content')
                                    ->schema([
                                        TextInput::make('tagline')->label('Tagline'),
                                        TextInput::make('section_1_title')->label('Title'),
                                        MarkdownEditor::make('section_1_text')->label('Text'),
                                        FileUpload::make('section_1_image')->label('Background Image')->directory('home-sections'),
                                        TextInput::make('section_1_cta_text')
                                        ->label('CTA Text')
                                        ->maxLength(80),
                                        TextInput::make('section_1_cta_link')
                                            ->label('CTA Link')
                                            ->rule('url') 
                                            ->placeholder('https://...'),
                                    ])->columns(1),
                            ]),

                        Tabs\Tab::make('Section 2')
                            ->schema([
                                Section::make('Section 2 Content')
                                    ->schema([
                                        TextInput::make('section_2_title')->label('Title'),
                                        MarkdownEditor::make('section_2_text')->label('Text'),
                                        FileUpload::make('section_2_image')->label('Image')->directory('home-sections'),
                                    ])->columns(1),
                            ]),

                        Tabs\Tab::make('Section 3')
                            ->schema([
                                Section::make('Section 3 Content')
                                    ->schema([
                                        TextInput::make('section_3_title')->label('Title'),
                                        MarkdownEditor::make('section_3_text')->label('Text'),
                                        FileUpload::make('section_3_image')->label('Image')->directory('home-sections'),
                                    ])->columns(1),
                            ]),

            Tabs\Tab::make('Categories Section')
                ->schema([
                    Repeater::make('categories_boxes')
                        ->label('Category Boxes')
                        ->schema([
                            FileUpload::make('image')
                                ->label('Image')
                                ->directory('home-sections/categories')
                                ->image()
                                ->required(),
                            TextInput::make('link')
                                ->label('Redirect Link')
                                ->required(),
                        ])
                        ->default([])
                        ->columns(2)
                        ->addActionLabel('Add Category Box'),
                ]),

            Tabs\Tab::make('Banners')
                ->schema([
                    Section::make('Banner 1')
                        ->schema([
                            FileUpload::make('banner_1_image')
                                ->label('Banner 1 Image')
                                ->directory('home-sections/banners')
                                ->image(),
                            TextInput::make('banner_1_link')
                                ->label('Banner 1 Link')
                                ->rule('url')
                                ->placeholder('https://...'),
                         
                            // TextInput::make('banner_1_link')
                            //     ->label('Banner 1 Link'),
                        ])
                        ->columns(2),

                    Section::make('Banner 2')
                        ->schema([
                            FileUpload::make('banner_2_image')
                                ->label('Banner 2 Image')
                                ->directory('home-sections/banners')
                                ->image(),

                            TextInput::make('banner_2_link')
                                ->label('Banner 2 Link')
                                ->rule('url')
                                ->placeholder('https://...'),

                            // TextInput::make('banner_2_link')
                            //     ->label('Banner 2 Link'),
                        ])
                        ->columns(2),
                ]),

            Tabs\Tab::make('Blog Section')
                ->schema([
                    Section::make('Blog Section Content')
                        ->schema([
                            TextInput::make('blog_section_title')->label('Title'),
                            MarkdownEditor::make('blog_section_text')->label('Text'),
                        ])
                        ->columns(1),
                ]),
                Tabs\Tab::make('SEO')
                    ->icon('heroicon-o-magnifying-glass-circle')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('meta_description')
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
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime('d M Y H:i')
            ])
            ->defaultSort('id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomeSections::route('/'),
            // 'create' => Pages\CreateHomeSection::route('/create'),
            'edit' => Pages\EditHomeSection::route('/{record}/edit'),
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
