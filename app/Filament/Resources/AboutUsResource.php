<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutUsResource\Pages;
use App\Models\AboutUs;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class AboutUsResource extends BaseResource
{
    protected static ?string $model = AboutUs::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $navigationLabel = 'About Us';

    protected static ?string $title = 'About Us';

    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('AboutUsTabs')
                    ->tabs([
                        // Forms\Components\Tabs\Tab::make('Slider & First Section')
                        //     ->icon('heroicon-o-photo')
                        //     ->schema([
                        //         Forms\Components\FileUpload::make('slider_1_image')
                        //             ->label(__('Slider Image 1'))
                        //             ->directory('about-us')
                        //             ->image()
                        //             ->maxSize(5120),

                        //         Forms\Components\FileUpload::make('slider_2_image')
                        //             ->label(__('Slider Image 2'))
                        //             ->directory('about-us')
                        //             ->image()
                        //             ->maxSize(5120),

                        //         Forms\Components\FileUpload::make('intro_image')
                        //             ->label(__('Intro Image'))
                        //             ->directory('about-us')
                        //             ->image()
                        //             ->maxSize(5120),

                        //         Forms\Components\TextInput::make('first_section_title')
                        //             ->label(__('First Section Title')),

                        //         Forms\Components\Textarea::make('first_section_desc')
                        //             ->label(__('First Section Description'))
                        //             ->columnSpanFull(),
                        //     ]),

                        Forms\Components\Tabs\Tab::make('Introduction')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\TextInput::make('intro_title')->required(),
                                Forms\Components\MarkdownEditor::make('intro_text')->required(),
                                Forms\Components\TextInput::make('intro_cta'),
                                Forms\Components\TextInput::make('intro_url'),
                                Forms\Components\SpatieMediaLibraryFileUpload::make('about_us_video')
                                    ->label(__('Intro Video'))
                                    ->collection('about_us_video')
                                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                                    ->maxSize(51200) 

                            ]),

                        Forms\Components\Tabs\Tab::make('Center Section')
                            ->icon('heroicon-o-bars-3-center-left')
                            ->schema([
                                Forms\Components\TextInput::make('center_title')->required(),
                                Forms\Components\MarkdownEditor::make('center_text')->required(),
                                // Forms\Components\TextInput::make('center_cta'),
                                // Forms\Components\TextInput::make('center_url'),
                                // Forms\Components\FileUpload::make('center_image_path')
                                //     ->label(__('Center Image'))
                                //     ->directory('about-us')
                                //     ->image()
                                //     ->maxSize(5120),
                            ]),

                        Forms\Components\Tabs\Tab::make('Latest Section')
                            ->icon('heroicon-o-newspaper')
                            ->schema([
                                Forms\Components\TextInput::make('latest_title')->required(),
                                Forms\Components\MarkdownEditor::make('latest_text')->required(),
                                Forms\Components\FileUpload::make('latest_image_path')
                                    ->label(__('Latest Image'))
                                    ->directory('about-us')
                                    ->image()
                                    ->maxSize(5120),
                            ]),
                        Forms\Components\Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass-circle')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->maxLength(500),

                                Forms\Components\TextInput::make('alt_text')
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
                Tables\Columns\TextColumn::make('intro_title')->label('Introduction Title'),
                // Tables\Columns\TextColumn::make('center_title')->label('Center Title'),
                Tables\Columns\TextColumn::make('latest_title')->label('Latest Title'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAboutUs::route('/'),
            'edit' => Pages\EditAboutUs::route('/{record}/edit'),
        ];
    }

    public static function getNotifications(): array
    {
        return [
            'saved' => Notification::make()
                ->title('About Us updated successfully')
                ->success(),
        ];
    }
}
