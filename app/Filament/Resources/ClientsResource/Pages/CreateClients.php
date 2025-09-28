<?php

namespace App\Filament\Resources\ClientsResource\Pages;

use App\Filament\Resources\ClientsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateClients extends CreateRecord
{
    protected static string $resource = ClientsResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Combine first_name and last_name into name
        if (isset($data['first_name']) && isset($data['last_name'])) {
            $data['name'] = trim($data['first_name'] . ' ' . $data['last_name']);
        }
        
        // Set display name if provided
        if (isset($data['display_name'])) {
            $data['desc_for_comment'] = $data['display_name'];
        }
        
        return $data;
    }
    
    protected function afterCreate(): void
    {
        // Assign client role
        $this->record->assignRole('client');
    }
}
