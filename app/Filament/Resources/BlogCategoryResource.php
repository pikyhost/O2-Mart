<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\BlogCategoryResource\Pages;
use App\Models\BlogCategory;
use Closure;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BlogCategoryResource extends BaseResource
{
    protected static ?string $model = BlogCategory::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Blogs Management';

    public static function getNavigationLabel(): string
    {
        return 'Blog Categories';
    }

    public static function getLabel(): ?string
    {
        return 'Blog Category';
    }

    public static function getPluralLabel(): ?string
    {
        return 'Blog Categories';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->columnSpanFull()
                        ->required()
                        ->maxLength(255)
                        ->label('Name'),

                    SelectTree::make('parent_id')
                        ->columnSpanFull()
                        ->label('Parent Category')
                        ->searchable()
                        ->enableBranchNode()
                        ->relationship('parent', 'name', 'parent_id')
                        ->placeholder('Select a parent category')
                        ->rules([
                            fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                if ($value === $get('id')) {
                                    $fail('A category cannot be its own parent.');
                                }
                            },
                        ])
                        ->validationAttribute('Parent Category'),

                    Forms\Components\Textarea::make('description')
                        ->columnSpanFull()
                        ->maxLength(65535)
                        ->columnSpanFull()
                        ->label('Description'),

                    Forms\Components\Checkbox::make('is_active')
                        ->columnSpanFull()
                        ->default(true)
                        ->label('Active'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Name'),
                TextColumn::make('parent.name')
                    ->badge()
                    ->label('Parent Category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50)
                    ->label('Description'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created At'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Updated At'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->modalHeading('Delete Blog Category')
                    ->modalDescription('Are you sure you want to delete this category?')
                    ->modalSubmitActionLabel('Yes, delete')
                    ->modalCancelActionLabel('No, cancel'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->modalHeading('Delete Selected Categories')
                        ->modalDescription('Are you sure you want to delete the selected categories?')
                        ->modalSubmitActionLabel('Yes, delete')
                        ->modalCancelActionLabel('No, cancel'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogCategories::route('/'),
            'create' => Pages\CreateBlogCategory::route('/create'),
            'edit' => Pages\EditBlogCategory::route('/{record}/edit'),
        ];
    }
}
