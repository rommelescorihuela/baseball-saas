<?php

namespace App\Filament\App\Resources\CategoryResource\Pages;

use App\Filament\App\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        if ($tenant) {
            $data['league_id'] = $tenant->id;
        }

        return $data;
    }
}