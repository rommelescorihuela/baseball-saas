<?php

namespace App\Filament\Resources\PlayerStats\Pages;

use App\Filament\Resources\PlayerStats\PlayerStatResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPlayerStat extends ViewRecord
{
    protected static string $resource = PlayerStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
