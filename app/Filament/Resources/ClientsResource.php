<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Models\User;
use App\Filament\Resources\ClientsResource\Pages;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientsResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Clients Management';
    protected static ?string $navigationLabel = 'Clients';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            //  ClientsResource::form()
            Forms\Components\TextInput::make('first_name')
                ->label('First Name')
                ->afterStateHydrated(function (Forms\Components\TextInput $component) {
                    $record = $component->getRecord();
                    $component->state($record?->first_name); 
                })
                ->dehydrated(fn ($state) => filled($state)) 
                ->required(fn (string $operation) => $operation === 'create'),

            Forms\Components\TextInput::make('last_name')
                ->label('Last Name')
                ->afterStateHydrated(function (Forms\Components\TextInput $component) {
                    $record = $component->getRecord();
                    $component->state($record?->last_name);
                })
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation) => $operation === 'create'),

            Forms\Components\TextInput::make('display_name')
                ->label('Display Name')
                ->afterStateHydrated(function (Forms\Components\TextInput $component) {
                    $record = $component->getRecord();
                    $component->state($record?->display_name);
                })
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation) => $operation === 'create'),



            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique('users', 'email', ignoreRecord: true),

            Forms\Components\TextInput::make('phone')
                ->label('Mobile Number')
                ->required(),

            Forms\Components\TextInput::make('password')
                ->label('Password')
                ->password()
                ->maxLength(255)
                ->revealable()
                ->rules([
                    'confirmed',
                    \Illuminate\Validation\Rules\Password::defaults()
                ])
                ->helperText('Minimum 8 characters, uppercase, lowercase, and symbols required')
                ->dehydrateStateUsing(fn ($state) => filled($state) ? \Illuminate\Support\Facades\Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation) => $operation === 'create'),

            Forms\Components\TextInput::make('password_confirmation')
                ->label('Confirm Password')
                ->password()
                ->same('password')
                ->revealable()
                ->dehydrated(false)
                ->requiredWith('password'),
        ]);
    }

   public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('first_name')
                    ->label('First name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_name')
                    ->label('Last name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('display_name')
                    ->label('Display Name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Mobile')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Search: Name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('desc_for_comment')
                    ->label('Search: Display')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Modified At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClients::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClients::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('roles', fn ($q) => $q->where('name', UserRole::Client->value));
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'desc_for_comment', 'email', 'phone'];
    }
}
