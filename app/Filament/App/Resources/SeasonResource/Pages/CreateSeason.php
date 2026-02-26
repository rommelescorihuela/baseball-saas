<?php

namespace App\Filament\App\Resources\SeasonResource\Pages;

use App\Filament\App\Resources\SeasonResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSeason extends CreateRecord
{
    protected static string $resource = SeasonResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        if ($tenant) {
            $data['league_id'] = $tenant->id;
        }

        return $data;
    }
}