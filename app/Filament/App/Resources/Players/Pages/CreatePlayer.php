<?php

namespace App\Filament\App\Resources\Players\Pages;

use App\Filament\App\Resources\Players\PlayerResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePlayer extends CreateRecord
{
    protected static string $resource = PlayerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asignar automáticamente el league_id del tenant actual
        $data['league_id'] = \Filament\Facades\Filament::getTenant()->id;

        // Registrar quién creó el jugador (trazabilidad)
        $data['created_by'] = Auth::id();

        return $data;
    }
}
