<?php

namespace App\Filament\Resources\ViscosityGradeResource\Pages;

use App\Filament\Resources\ViscosityGradeResource;
use Filament\Actions;
use App\Filament\Resources\Pages\BaseListPage;

class ListViscosityGrades extends BaseListPage
{
    protected static string $resource = ViscosityGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
