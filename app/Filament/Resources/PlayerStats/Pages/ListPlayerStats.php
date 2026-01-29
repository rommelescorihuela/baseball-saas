<?php

namespace App\Filament\Resources\PlayerStats\Pages;

use App\Filament\Resources\PlayerStats\PlayerStatResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPlayerStats extends ListRecords
{
    protected static string $resource = PlayerStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
