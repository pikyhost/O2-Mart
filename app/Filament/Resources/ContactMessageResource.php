<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;

class ContactMessageResource extends BaseResource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.Groups.communication');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.Labels.contact_messages');
    }

    public static function getModelLabel(): string
    {
        return __('models.contact_message.singular');
    }

    public static function getPluralLabel(): ?string
    {
        return __('models.contact_message.plural');
    }

    public static function getLabel(): ?string
    {
        return __('models.contact_message.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.contact_message.plural_model');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->default(fn() => auth()->user()->name)
                    ->columnSpanFull()
                    ->label(__('fields.name'))
                    ->required()
                    ->maxLength(255),

                // PhoneInput::make('phone')
                //     ->columnSpanFull()
                //     ->separateDialCode(true)
                //     ->enableIpLookup(true)
                //     ->required()
                //     ->label(__('Phone')),

                Forms\Components\TextInput::make('email')
                    ->default(fn() => auth()->user()->email)
                    ->columnSpanFull()
                    ->label(__('fields.email'))
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('subject')
                    ->columnSpanFull()
                    ->label(__('fields.subject'))
                    ->maxLength(255),
                Forms\Components\Textarea::make('message')
                    ->label(__('fields.message'))
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('fields.name')),
                Tables\Columns\TextColumn::make('email')->label(__('fields.email')),
                // Tables\Columns\TextColumn::make('phone')->label(__('Phone')),
                Tables\Columns\TextColumn::make('subject')->label(__('fields.subject')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(__('actions.view')),
                Tables\Actions\DeleteAction::make()->label(__('actions.delete')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label(__('actions.bulk_delete')),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(2)
            ->schema([
                Components\Section::make()
                    ->schema([
                        Components\TextEntry::make('user.name')
                            ->badge()
                            ->label(__('From Account'))
                            ->url(function (ContactMessage $record) {
                                return UserResource::getUrl('view', ['record' => $record->user_id]);
                            }, true)
                            ->visible(fn ($record) => $record->user_id),

                        Components\TextEntry::make('name')
                            ->label(__('fields.name')),

                        // PhoneEntry::make('phone')
                        //     ->placeholder('-')
                        //     ->label(__('Phone number')),

                        Components\TextEntry::make('email')
                            ->label(__('fields.email')),

                        Components\TextEntry::make('ip_address')
                            ->label(__('fields.ip_address')),

                        // Components\TextEntry::make('sender_country')
                        //     ->label(__('fields.sender_country')),
                    ])->columns(2)
                    ->heading(__('Sender Information')),

                Components\Section::make()
                    ->schema([
                        Components\TextEntry::make('subject')
                            ->label(__('fields.subject'))
                            ->columnSpanFull(),

                        Components\TextEntry::make('message')
                            ->label(__('fields.message'))
                            ->columnSpanFull(),
                    ])
                    ->heading(__('Message Details'))
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
        ];
    }
}
