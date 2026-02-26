<?php

namespace App\Filament\App\Resources\Players\Pages;

use App\Filament\App\Resources\Players\PlayerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPlayer extends ViewRecord
{
    protected static string $resource = PlayerResource::class;

    protected string $view = 'filament.app.resources.players.pages.view-player';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
