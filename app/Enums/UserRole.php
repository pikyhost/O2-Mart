<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum UserRole: string implements HasColor, HasLabel
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Client = 'client';
    case Blogger = 'blogger';


    /**
     * Get the translated label for the role.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::SuperAdmin => __('Super Admin'),
            self::Admin => __('Admin'),
            self::Client => __('Client'),
            self::Blogger => 'Blogger',
        };
    }

    /**
     * Get the color for the role.
     */
    public function getColor(): string
    {
        return match ($this) {
            self::SuperAdmin => 'danger',
            self::Admin => 'success',
            self::Client => 'info',
            self::Blogger => 'gray',
        };
    }

    public static function getLabelFor(string $role): string
    {
        return self::tryFrom($role)?->getLabel() ?? Str::headline($role);
    }

    public static function getColorFor(string $role): string
    {
        return self::tryFrom($role)?->getColor() ?? 'warning';
    }
}
