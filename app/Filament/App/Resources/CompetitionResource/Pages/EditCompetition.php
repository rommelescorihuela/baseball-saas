<?php

namespace App\Filament\App\Resources\CompetitionResource\Pages;

use App\Filament\App\Resources\CompetitionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompetition extends EditRecord
{
    protected static string $resource = CompetitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}