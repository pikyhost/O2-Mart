<?php

namespace App\Traits;

use App\Services\PdfDownloadService;
use Filament\Actions\Action;

trait HasPdfDownloadAction
{
    protected function getPdfDownloadAction(string $viewPath, string $fileName, array $relationships = [], ?string $color = 'gray'): Action
    {
        return Action::make('download_pdf')
            ->label('Extract to PDF')
            ->icon('heroicon-o-arrow-down-tray')
            ->color($color)
            ->action(function () use ($viewPath, $fileName, $relationships) {
                return PdfDownloadService::createSingleRecordExport(
                    viewPath: $viewPath,
                    fileName: $fileName,
                    record: $this->getRecord(),
                    relationships: $relationships
                );
            });
    }
}
