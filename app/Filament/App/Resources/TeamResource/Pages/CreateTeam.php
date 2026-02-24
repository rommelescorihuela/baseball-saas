<?php

namespace App\Filament\App\Resources\TeamResource\Pages;

use App\Filament\App\Resources\TeamResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    protected array $ownerData = [];

    public function mount(): void
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        if (! $tenant->canCreateTeam()) {
            \Filament\Notifications\Notification::make()
                ->title('Límite de equipos alcanzado')
                ->body('Actualiza tu plan para crear más equipos.')
                ->warning()
                ->send();
            
            $this->redirect(TeamResource::getUrl('index'));
            return;
        }

        parent::mount();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['league_id'] = \Filament\Facades\Filament::getTenant()->id;
        $this->ownerData['name'] = $data['owner_name'];
        $this->ownerData['email'] = $data['owner_email'];

        unset($data['owner_name'], $data['owner_email']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $email = $this->ownerData['email'];
        $name = $this->ownerData['name'];
        
        $user = \App\Models\User::where('email', $email)->first();
        $password = null;

        if (! $user) {
            $password = \Illuminate\Support\Str::random(10);
            $user = \App\Models\User::create([
                'name' => $name,
                'email' => $email,
                'password' => \Illuminate\Support\Facades\Hash::make($password),
            ]);
        }

        // Context: We are in the App Panel, so we are inside a Tenant (League).
        // The team being created belongs to this League.
        // We want to assign 'team_owner' role to this user for THIS League.
        
        $league = \Filament\Facades\Filament::getTenant();
        
        // Ensure user is attached to the League (Tenant)
        if (! $user->leagues()->where('league_id', $league->id)->exists()) {
             $user->leagues()->attach($league);
        }

        // Set the team id for permission check (League ID = Team ID in Spatie config context)
        setPermissionsTeamId($league->id);
        
        if (! $user->hasRole('team_owner')) {
            $user->assignRole('team_owner');
        }

        // Attach user to the Team
        $this->record->users()->attach($user);

        // Send Invitation
        \Illuminate\Support\Facades\Mail::to($user)->send(
            new \App\Mail\TeamOwnerInvitation($user, $this->record, $password)
        );
    }
}