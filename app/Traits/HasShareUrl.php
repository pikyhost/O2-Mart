<?php

namespace App\Traits;

trait HasShareUrl
{
    public function getShareUrlAttribute(): string
    {
        $frontend = config('app.frontend_url');
        $type = $this->getShareType();

        return "{$frontend}/{$type}/{$this->id}";
    }

    protected function getShareType(): string
    {
        return '';
    }
}
