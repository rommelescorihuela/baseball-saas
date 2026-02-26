<?php

namespace App\Filament\App\Resources\GameResource\Pages;

use App\Filament\App\Resources\GameResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGame extends CreateRecord
{
    protected static string $resource = GameResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        if ($tenant) {
            $data['league_id'] = $tenant->id;
        }

        return $data;
    }
}