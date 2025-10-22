<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterSubscriberResource\Pages;
use App\Mail\OfferEmail;
use App\Models\NewsletterSubscriber;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewsletterSubscriberResource extends BaseResource
{
    protected static ?string $model = NewsletterSubscriber::class;

    public static function getNavigationLabel(): string
    {
        return __('Newsletter Subscribers');
    }

    public static function getModelLabel(): string
    {
        return __('Newsletter Subscriber');
    }

    public static function getLabel(): ?string
    {
        return __('Newsletter Subscriber');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Newsletter Subscribers');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Newsletter Subscribers');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Pages Settings Management');
    }

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull()
                    ->label(__('Email'))
                    ->email()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label(__('IP Address')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime(),


                Tables\Columns\TextColumn::make('verified_at')
                    ->label(__('Verified At'))
                    ->dateTime(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('Delete Selected')),
                    Tables\Actions\BulkAction::make('sendOfferOrMessage')
                        ->label(__('Send Offer or Update'))
                        ->icon('heroicon-o-paper-airplane')
                        ->form([
                            Select::make('type')
                                ->label(__('Choose what to send'))
                                ->options([
                                    'discount' => __('Promotional Discount'),
                                    'product'  => __('Product Highlight'),
                                    'article'  => __('Blog Article'),
                                    'custom'   => __('Custom Message'),
                                ])
                                ->required()
                                ->live(),

                            Select::make('discount_id')
                                ->label(__('Select a Discount'))
                                ->options(fn () => \App\Models\Discount::active()->pluck('name', 'id'))
                                ->visible(fn (Get $get) => $get('type') === 'discount')
                                ->required(fn (Get $get) => $get('type') === 'discount'),

                            Select::make('product_id')
                                ->label(__('Select a Product'))
                                ->options(fn () => \App\Models\Product::pluck('name', 'id'))
                                ->visible(fn (Get $get) => $get('type') === 'product')
                                ->required(fn (Get $get) => $get('type') === 'product'),

                            Select::make('blog_id')
                                ->label(__('Select a Blog Article'))
                                ->options(fn () => \App\Models\Blog::latest()->pluck('title', 'id'))
                                ->visible(fn (Get $get) => $get('type') === 'article')
                                ->required(fn (Get $get) => $get('type') === 'article'),

                            MarkdownEditor::make('custom_message')
                                ->label(__('Write your message'))
                                ->visible(fn (Get $get) => $get('type') === 'custom')
                                ->required(fn (Get $get) => $get('type') === 'custom')
                        ])
                        ->action(fn (Collection $records, array $data) => static::sendOffer($records, $data))
                        ->deselectRecordsAfterCompletion()
                ]),
        ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('Edit')),
                Tables\Actions\DeleteAction::make()->label(__('Delete')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNewsletterSubscribers::route('/'),
        ];
    }

    public static function sendOffer(Collection $users, array $data): void
    {
        try {
            foreach ($users as $user) {
                Mail::to($user->email)->sendNow(new OfferEmail($user, $data));
            }
        } catch (\Exception $e) {
            Log::error('Mail sending failed: ' . $e->getMessage(), []);
        }

        Notification::make()
            ->title(__('Your message was successfully sent!'))
            ->success()
            ->send();
    }
}
