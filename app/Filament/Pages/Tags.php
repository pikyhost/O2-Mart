<?php

namespace App\Filament\Pages;

use App\Models\Tag;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Tags extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    protected static string $view = 'filament.pages.tags';

    protected static ?string $navigationGroup = 'Blogs Management';

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Tags';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tags';
    }

    public static function getLabel(): ?string
    {
        return 'Tag';
    }

    public static function getPluralLabel(): ?string
    {
        return 'Tags';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Add New Tag')
                    ->schema([
                        TextInput::make('name')
                            ->columnSpanFull()
                            ->hiddenLabel()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn (string $state): string => ucwords($state)),
                    ]),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $formData = $this->form->getState();

        Tag::create([
            'name' => $formData['name'],
        ]);

        $this->form->fill([]); // Clear form state after creation

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        $columns = [
            TextColumn::make('id')
                ->label('ID')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('name')
                ->label('Tag Name')
                ->sortable()
                ->searchable(),

            TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('deleted_at')
                ->label('Deleted At')
                ->dateTime()
                ->toggleable(isToggledHiddenByDefault: true),
        ];

        return $table
            ->heading('Tags')
            ->description('Manage blog tags')
            ->query(Tag::latest())
            ->columns($columns)
            ->actions([
                ViewAction::make()->infolist(function () {
                    return [
                        TextEntry::make('id')->label('ID')->inlineLabel(),
                        TextEntry::make('name')->label('Tag Name')->inlineLabel(),
                        TextEntry::make('created_at')->label('Created At')->inlineLabel(),
                        TextEntry::make('updated_at')->label('Last Updated')->inlineLabel(),
                    ];
                }),

                EditAction::make()
                    ->slideOver()
                    ->form(function ($record) {
                        return [
                            TextInput::make('name')
                                ->columnSpanFull()
                                ->required()
                                ->unique('tags', 'name', ignorable: $record)
                                ->maxLength(255),
                        ];
                    }),

                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public function getSubmitAction(): array
    {
        return [
            Action::make('create')
                ->color('primary')
                ->label('Save')
                ->submit('create'),
        ];
    }
}
