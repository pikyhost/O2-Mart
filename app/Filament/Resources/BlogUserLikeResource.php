<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\BlogUserLikeResource\Pages;
use App\Models\BlogUserLike;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class BlogUserLikeResource extends BaseResource
{
    protected static ?string $model = BlogUserLike::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-thumb-up';

    protected static ?string $navigationGroup = 'Blogs Management';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return 'Users Likes'; // Navigation label for Favorite Articles
    }

    public static function getModelLabel(): string
    {
        return 'Favorite Article'; // Singular label for Favorite Article
    }

    public static function getLabel(): ?string
    {
        return 'Favorite Article'; // Singular label
    }

    public static function getBreadcrumb(): string
    {
        return 'Favorite Articles'; // Breadcrumb label for Favorite Articles
    }

    public static function getPluralLabel(): ?string
    {
        return 'Favorite Articles'; // Plural label for Favorite Articles
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('blog.title')
                    ->label('Article Title') // Blog Title in English
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User Name') // User Name in English
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added Date') // Added Date in English
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated Date') // Updated Date in English
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // User Filter
                SelectFilter::make('user_id')
                    ->label('User') // English label for User
                    ->relationship('user', 'name')
                    ->placeholder('All Users') // English placeholder
                    ->visible(fn () => auth()->user()->hasRole(UserRole::SuperAdmin->value)),

                // Blog Filter
                SelectFilter::make('blog_id')
                    ->label('Article') // English label for Article
                    ->relationship('blog', 'title')
                    ->placeholder('All Articles') // English placeholder
                    ->visible(fn () => auth()->user()->hasRole(UserRole::SuperAdmin->value)),
            ], Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(2);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogUserLikes::route('/'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
