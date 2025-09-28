<?php

namespace App\Traits;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;

trait AuthorizesShopActions
{
    /**
     * Check if the user can access the resource.
     */
    public static function canAccess(array $parameters = []): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return auth()->user()->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::SalonManager->value,
            UserRole::TailorManager->value,
        ]);
    }

    /**
     * Check if the user can edit the resource.
     */
    protected function canEdit(Model $record): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $user = auth()->user();
        $ownerRecord = $this->getOwnerRecord();

        if ($user->hasRole(UserRole::SuperAdmin->value)) {
            return true;
        }

        if ($user->hasAnyRole([UserRole::SalonManager->value, UserRole::TailorManager->value])) {
            return $ownerRecord->manager_id === $user->id;
        }

        return false;
    }

    /**
     * Check if the user can delete the resource.
     */
    protected function canDelete(Model $record): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $user = auth()->user();
        $ownerRecord = $this->getOwnerRecord();

        if ($user->hasRole(UserRole::SuperAdmin->value)) {
            return true;
        }

        if ($user->hasAnyRole([UserRole::SalonManager->value, UserRole::TailorManager->value])) {
            return $ownerRecord->manager_id === $user->id;
        }

        return false;
    }

    /**
     * Check if the user can view the resource.
     */
    protected function canView(Model $record): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $user = auth()->user();
        $ownerRecord = $this->getOwnerRecord();

        if ($user->hasRole(UserRole::SuperAdmin->value)) {
            return true;
        }

        if ($user->hasAnyRole([UserRole::SalonManager->value, UserRole::TailorManager->value])) {
            return $ownerRecord->manager_id === $user->id;
        }

        return false;
    }

    /**
     * Check if the user can view any resources.
     */
    protected function canViewAny(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return auth()->user()->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::SalonManager->value,
            UserRole::TailorManager->value,
        ]);
    }

    protected function canCreate(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return auth()->user()->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::SalonManager->value,
            UserRole::TailorManager->value,
        ]);
    }
}
