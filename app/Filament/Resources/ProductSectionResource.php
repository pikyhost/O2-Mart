<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductSectionResource\Pages;
use App\Models\ProductSection;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductSectionResource extends Resource
{
    protected static ?string $model = ProductSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('ProductSectionTabs')
                ->tabs([
                    // --- Main Section Tab (Dynamic Label) ---
                    Forms\Components\Tabs\Tab::make('Section')
                        ->label(fn ($get) => match ($get('type')) {
                            'auto_part' => 'Auto Parts Section',
                            'tyre'      => 'Tyres Section',
                            'battery'   => 'Batteries Section',
                            'rim'       => 'Rims Section',
                            default     => 'Product Section',
                        })
                        ->schema(function ($get) {
                            return self::sectionForm($get('type'));
                        }),

                    // --- SEO Tab ---
                    Forms\Components\Tabs\Tab::make('SEO')
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

    protected static function sectionForm(string $type): array
    {
        return [
            Hidden::make('type')
                ->default($type)
                ->disabled()
                ->dehydrated(),

            FileUpload::make('background_image')
                ->label('Background Image')
                ->directory('product-sections')
                ->image(),

            TextInput::make('section1_title')->label('Section 1 Title'),
            MarkdownEditor::make('section1_text1')->label('Section 1 - Part 1'),
            MarkdownEditor::make('section1_text2')->label('Section 1 - Part 2'),

            TextInput::make('section2_title')->label('Section 2 Title'),
            MarkdownEditor::make('section2_text')->label('Section 2 Text'),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->label('Type')->sortable(),
                Tables\Columns\TextColumn::make('section1_title')->label('Section 1 Title')->limit(30),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductSections::route('/'),
            'edit'  => Pages\EditProductSection::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
