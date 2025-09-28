<?php

namespace App\Traits;

use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

trait HasCreatedAtFilter
{
    public static function getCreatedAtFilter(): Filter
    {
        return Filter::make('created_at')
            ->indicator(__('Creation Date Range'))
            ->form([
                DateTimePicker::make('created_from')
                    ->label(__('Creation date from'))
                    ->native(false) // Ensures a proper date picker UI
                    ->withoutSeconds() // Simplifies selection to minute precision
                    ->maxDate(now()) // Prevents selecting future dates
                    ->afterStateUpdated(fn (HasForms $livewire, DateTimePicker $component) =>
                    $livewire->validateOnly($component->getStatePath())
                    )
                    ->columnSpan(3),

                DateTimePicker::make('created_until')
                    ->label(__('Creation date until'))
                    ->native(false)
                    ->withoutSeconds()
                    ->maxDate(now())
                    ->afterOrEqual('created_from') // Ensures proper range selection
                    ->afterStateUpdated(fn (HasForms $livewire, DateTimePicker $component) =>
                    $livewire->validateOnly($component->getStatePath())
                    )
                    ->columnSpan(3),
            ])
            ->columns(['sm' => 6, 'lg' => null])
            ->query(fn (Builder $query, array $data) => $query
                ->when(isset($data['created_from']) && !empty($data['created_from']), fn (Builder $query) =>
                $query->where('created_at', '>=', Carbon::parse($data['created_from']))
                )
                ->when(isset($data['created_until']) && !empty($data['created_until']), fn (Builder $query) =>
                $query->where('created_at', '<=', Carbon::parse($data['created_until']))
                )
            )
            ->indicateUsing(function (array $data): ?string {
                $from = $data['created_from'] ?? null;
                $until = $data['created_until'] ?? null;

                if ($from && $until) {
                    return __('Creation date: from :date_from to :date_until', [
                        'date_from' => Carbon::parse($from)->toFormattedDateString(),
                        'date_until' => Carbon::parse($until)->toFormattedDateString()
                    ]);
                }

                if ($from) {
                    return __('Creation date: from :date_from', [
                        'date_from' => Carbon::parse($from)->toFormattedDateString()
                    ]);
                }

                if ($until) {
                    return __('Creation date: until :date_until', [
                        'date_until' => Carbon::parse($until)->toFormattedDateString()
                    ]);
                }

                return null;
            });
    }
}
