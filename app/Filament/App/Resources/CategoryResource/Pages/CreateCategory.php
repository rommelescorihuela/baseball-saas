<?php

namespace App\Filament\App\Resources\CategoryResource\Pages;

use App\Filament\App\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function beforeCreate(): void
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        if ($tenant && !$tenant->canCreateCategory()) {
            \Filament\Notifications\Notification::make()
                ->title('Límite de categorías alcanzado')
                ->body('Su plan actual no permite crear más categorías.')
                ->danger()
                ->send();

            $this->halt();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        if ($tenant) {
            $data['league_id'] = $tenant->id;
        }

        return $data;
    }
}