<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\Importer;
use Illuminate\Database\Eloquent\Model;


abstract class BaseUpsertImporter extends Importer
{
    /** @var class-string<Model> */
    protected static ?string $model = null;

    /** @var array<int, string> */
    protected static array $uniqueBy = [];

    public function resolveRecord(): ?Model
    {
        $modelClass = static::$model;
        if (!$modelClass) {
            throw new \RuntimeException(static::class.' must set static::$model');
        }

        if (empty(static::$uniqueBy)) {
            return new $modelClass();
        }

        $query = $modelClass::query();

        foreach (static::$uniqueBy as $column) {
            $value = data_get($this->data, $column);
            if ($value === null || $value === '') {
                return new $modelClass();
            }
            $query->where($column, $value);
        }

        return $query->first() ?: new $modelClass();
    }

    public function fillRecord(): void
    {
        $modelClass = static::$model;
        $model = new $modelClass();
        $fillable = $model->getFillable();
        
        if (empty($fillable)) {
            // Model uses $guarded, fill all data except guarded fields
            $guarded = $model->getGuarded();
            $payload = collect($this->data)->except($guarded)->toArray();
        } else {
            // Model uses $fillable
            $payload = collect($this->data)->only($fillable)->toArray();
        }
        
        $this->record->fill($payload);
    }
}
