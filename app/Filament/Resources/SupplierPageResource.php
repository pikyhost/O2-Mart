<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierPageResource\Pages;
use App\Models\SupplierPage;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class SupplierPageResource extends BaseResource
{
    protected static ?string $model = SupplierPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Suppliers';

    protected static ?string $navigationLabel = 'Page Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Section::make()->schema([
                   TextInput::make('title_become_supplier')->label('Become Supplier Title')->required(),
                   MarkdownEditor::make('desc_become_supplier')
                       ->label('Become Supplier Description')
                       ->required()
                       ->columnSpanFull()
                       ->toolbarButtons([
                           'attachFiles',
                           'blockquote',
                           'bold',
                           'bulletList',
                           'codeBlock',
                           'heading',
                           'italic',
                           'link',
                           'orderedList',
                           'redo',
                           'strike',
                           'table',
                           'undo',
                       ]),
                   TextInput::make('why_auto_title')->label('Why Auto Title')->required(),
                   MarkdownEditor::make('why_auto_desc')
                       ->label('Why Auto Description')
                       ->required()
                       ->columnSpanFull()
                       ->toolbarButtons([
                           'attachFiles',
                           'blockquote',
                           'bold',
                           'bulletList',
                           'codeBlock',
                           'heading',
                           'italic',
                           'link',
                           'orderedList',
                           'redo',
                           'strike',
                           'table',
                           'undo',
                       ]), 
                  ])->columns(1),
                  
               Section::make('SEO')
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
                   ])->columns(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_become_supplier')->label('Become Supplier Title')->limit(50),
                TextColumn::make('why_auto_title')->label('Why Auto Title')->limit(50),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupplierPages::route('/'),
            'edit' => Pages\EditSupplierPage::route('/{record}/edit'),
        ];
    }
}
