<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    FileUpload ::make('background_image')
                        ->label('Background Image')
                        ->directory('faq')
                        ->image()
                        ->columnSpan('full'),
                    Repeater::make('items')
                        ->label('FAQ Items')
                        ->schema([
                            TextInput::make('question')
                                ->label('Question')
                                ->required(),
                            Textarea::make('answer')
                                ->label('Answer')
                                ->required(),
                            Select::make('category')
                                ->options([
                                    'general' => 'General Question',
                                    'payment' => 'Payment & Gift Card',
                                ])
                                ->required()
                                ->label('Category'),

                        ])
                        ->columnSpan('full')
                        ->collapsible()
                        ->reorderable(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('updated_at')->label('Last Modified At')->since(),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(\App\Filament\Imports\FaqImporter::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
