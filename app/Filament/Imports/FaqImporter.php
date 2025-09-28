<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use App\Models\Faq;
use Illuminate\Support\Str;

class FaqImporter extends Importer
{
    protected static ?string $model = Faq::class;

    protected array $allowedCategories = ['general', 'payment'];

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('question')->label('Question')->rules(['required','string','min:3']),
            ImportColumn::make('answer')->label('Answer')->rules(['required','string','min:1']),
            ImportColumn::make('category')->label('Category')->rules(['required','in:general,payment']),
        ];
    }

    public function resolveRecord(): ?Faq
    {
        return Faq::first() ?? new Faq();
    }

    public function fillRecord(): void
    {
        /** @var Faq $faq */
        $faq = $this->getRecord();
        $row = $this->getData();

        if ($faq->exists) {
            $faq->refresh();
        }

        $current = $faq->items;
        if (is_string($current)) {
            $decoded = json_decode($current, true);
            $current = is_array($decoded) ? $decoded : [];
        }
        if (! is_array($current)) {
            $current = [];
        }

        $category = strtolower(trim((string)($row['category'] ?? 'general')));
        if (! in_array($category, $this->allowedCategories, true)) {
            $category = 'general';
        }

        $question = trim((string)($row['question'] ?? ''));
        $answer   = trim((string)($row['answer'] ?? ''));
        if ($question === '' || $answer === '') {
            return;
        }

        $newItem = ['question' => $question, 'answer' => $answer, 'category' => $category];
        $makeKey = fn (array $i) => mb_strtolower(trim(($i['category'] ?? '') . '|' . ($i['question'] ?? '')));
        $keyToFind = $makeKey($newItem);

        $updated = false;
        foreach ($current as $idx => $item) {
            if ($makeKey($item) === $keyToFind) {
                $current[$idx] = $newItem;
                $updated = true;
                break;
            }
        }
        if (! $updated) {
            $current[] = $newItem;
        }

        $faq->items = array_values($current);

        if (! empty($row['background_image'])) {
            $faq->background_image = $row['background_image'];
        }
    }

    public static function getCompletedNotificationTitle(Import $import): string
    {
        return 'FAQ Import Completed';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'FAQ import has completed. ' . number_format($import->successful_rows) . ' ' . Str::plural('row', $import->successful_rows) . ' imported.';
        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failed) . ' ' . Str::plural('row', $failed) . ' failed.';
        }
        return $body;
    }
}
