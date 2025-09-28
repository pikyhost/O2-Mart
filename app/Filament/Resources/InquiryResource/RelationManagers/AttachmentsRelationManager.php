<?php

namespace App\Filament\Resources\InquiryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('file_type')
                    ->options([
                        'license' => 'License',
                        'part_photo' => 'Part Photo',
                        'other' => 'Other',
                    ])
                    ->required(),

                Forms\Components\FileUpload::make('path')
                    ->required()
                    ->directory('inquiry-attachments')
                    ->preserveFilenames(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_type')
            ->columns([
                Tables\Columns\TextColumn::make('file_type')
                    ->badge(),

                Tables\Columns\ImageColumn::make('path'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
