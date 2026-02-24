<?php

namespace App\Filament\Resources\Leagues\Pages;

use App\Filament\Resources\Leagues\LeagueResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeague extends CreateRecord
{
    protected static string $resource = LeagueResource::class;

    protected array $ownerData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->ownerData['name'] = $data['owner_name'];
        $this->ownerData['email'] = $data['owner_email'];
        $this->ownerData['password'] = $data['owner_password'];

        unset($data['owner_name'], $data['owner_email'], $data['owner_password']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $user = \App\Models\User::create([
            'name' => $this->ownerData['name'],
            'email' => $this->ownerData['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($this->ownerData['password']),
        ]);

        $user->assignRole('league_owner');
        
        $this->record->users()->attach($user);

        \Illuminate\Support\Facades\Mail::to($user)->send(
            new \App\Mail\LeagueOwnerCreated($user, $this->ownerData['password'])
        );
    }
}
