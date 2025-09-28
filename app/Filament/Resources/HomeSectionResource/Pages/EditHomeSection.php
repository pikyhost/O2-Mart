<?php

namespace App\Filament\Resources\HomeSectionResource\Pages;

use App\Filament\Resources\HomeSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomeSection extends EditRecord
{
    protected static string $resource = HomeSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
   public function mutateFormDataBeforeSave(array $data): array
    {
        \Log::info('💾 Saving HomeSection [' . $this->record->id . ']', $data);
        return $data;
    }

    

}
