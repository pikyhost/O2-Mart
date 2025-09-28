<?php

namespace App\Filament\Resources\ViscosityGradeResource\Pages;

use App\Filament\Resources\ViscosityGradeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditViscosityGrade extends EditRecord
{
    protected static string $resource = ViscosityGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
