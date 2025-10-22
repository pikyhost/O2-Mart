<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PolicyResource\Pages;
use App\Models\Policy;
use Filament\Forms\Components\MarkdownEditor;

use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PolicyResource extends BaseResource
{
    protected static ?string $model = Policy::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?int $navigationSort = 100;

    public static function getNavigationLabel(): string
    {
        return 'Policies';
    }

    public static function getModelLabel(): string
    {
        return 'Policy';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Policies';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings Management';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Policies')->tabs([
                Tab::make('Privacy Policy')
                    ->schema([
                        MarkdownEditor::make('privacy_policy')
                            ->label('Privacy Policy')
                            ->columnSpanFull(),
                    ]),
                Tab::make('Returns and Warranty Policy')
                    ->schema([
                        MarkdownEditor::make('refund_policy')
                            ->label('Returns and Warranty Policy')
                            ->columnSpanFull(),
                    ]),
                Tab::make('Terms of Service')
                    ->schema([
                        MarkdownEditor::make('terms_of_service')
                            ->label('Terms of Service')
                            ->columnSpanFull(),
                    ]),
                Tab::make('Privacy Policy SEO')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('meta_title_privacy_policy')
                            ->label('Meta Title')
                            ->maxLength(255),
                        \Filament\Forms\Components\Textarea::make('meta_description_privacy_policy')
                            ->label('Meta Description')
                            ->maxLength(500),
                        \Filament\Forms\Components\TextInput::make('alt_text_privacy_policy')
                            ->label('Alt Text')
                            ->maxLength(255),
                    ]),
                Tab::make('Refund Policy SEO')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('meta_title_refund_policy')
                            ->label('Meta Title')
                            ->maxLength(255),
                        \Filament\Forms\Components\Textarea::make('meta_description_refund_policy')
                            ->label('Meta Description')
                            ->maxLength(500),
                        \Filament\Forms\Components\TextInput::make('alt_text_refund_policy')
                            ->label('Alt Text')
                            ->maxLength(255),
                    ]),
                Tab::make('Terms of Service SEO')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('meta_title_terms_of_service')
                            ->label('Meta Title')
                            ->maxLength(255),
                        \Filament\Forms\Components\Textarea::make('meta_description_terms_of_service')
                            ->label('Meta Description')
                            ->maxLength(500),
                        \Filament\Forms\Components\TextInput::make('alt_text_terms_of_service')
                            ->label('Alt Text')
                            ->maxLength(255),
                    ]),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('privacy_policy')
                    ->label('Privacy Policy')
                    ->limit(50),
                TextColumn::make('refund_policy')
                    ->label('Refund Policy')
                    ->limit(50),
                TextColumn::make('terms_of_service')
                    ->label('Terms of Service')
                    ->limit(50),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPolicies::route('/'),
            'edit' => Pages\EditPolicy::route('/{record}/edit'),
        ];
    }
}
