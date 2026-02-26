<?php

namespace App\Filament\App\Resources\CompetitionResource\Pages;

use App\Filament\App\Resources\CompetitionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCompetition extends CreateRecord
{
    protected static string $resource = CompetitionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        if ($tenant) {
            $data['league_id'] = $tenant->id;
        }

        return $data;
    }
}