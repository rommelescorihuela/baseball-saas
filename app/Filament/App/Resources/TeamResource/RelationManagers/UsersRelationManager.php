<?php

namespace App\Filament\App\Resources\TeamResource\RelationManagers;

use App\Mail\TeamStaffInvitation;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Miembros del Equipo';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                \Filament\Forms\Components\TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation) => $operation === 'create')
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->visible(fn (string $operation) => $operation === 'create')
                    ->default(fn () => Str::random(10))
                    ->hint('Se generará automáticamente si se deja vacío'),
                \Filament\Forms\Components\Select::make('role')
                    ->label('Rol')
                    ->options([
                        'secretary' => 'Secretaría',
                        'coach' => 'Coach',
                    ])
                    ->required()
                    ->default('coach')
                    ->visible(fn (string $operation) => $operation === 'create')
                    ->helperText('La Secretaría gestiona jugadores y documentos. El Coach registra estadísticas.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Rol')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'secretary' => 'Secretaría',
                        'coach' => 'Coach',
                        'team_owner' => 'Dueño de Equipo',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'secretary' => 'info',
                        'coach' => 'success',
                        'team_owner' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Agregado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Agregar Miembro')
                    ->modalHeading('Agregar Miembro al Equipo')
                    ->modalDescription('Se enviará un correo con las credenciales de acceso.')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Generar contraseña si no se proporcionó
                        if (empty($data['password'])) {
                            $data['password'] = Hash::make(Str::random(10));
                        }

                        return $data;
                    })
                    ->after(function (array $data, RelationManager $livewire, $record) {
                        $team = $livewire->getOwnerRecord();
                        $league = \Filament\Facades\Filament::getTenant();

                        // Asociar usuario a la liga
                        if (! $record->leagues()->where('league_id', $league->id)->exists()) {
                            $record->leagues()->attach($league);
                        }

                        // Asignar rol
                        $role = $data['role'] ?? 'coach';
                        setPermissionsTeamId($league->id);
                        if (! $record->hasRole($role)) {
                            $record->assignRole($role);
                        }

                        // Enviar email con credenciales
                        $password = $data['password'] ?? Str::random(10);
                        Mail::to($record)->send(new TeamStaffInvitation($record, $team, $password));
                    }),
                AttachAction::make()
                    ->label('Agregar Usuario Existente')
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Usuario')
                            ->searchable()
                            ->preload(),
                        \Filament\Forms\Components\Select::make('role')
                            ->label('Rol')
                            ->options([
                                'secretary' => 'Secretaría',
                                'coach' => 'Coach',
                            ])
                            ->required()
                            ->default('coach'),
                    ])
                    ->after(function (array $data, RelationManager $livewire, $record) {
                        $league = \Filament\Facades\Filament::getTenant();

                        // Asociar usuario a la liga si no está
                        if (! $record->leagues()->where('league_id', $league->id)->exists()) {
                            $record->leagues()->attach($league);
                        }

                        // Asignar rol
                        $role = $data['role'] ?? 'coach';
                        setPermissionsTeamId($league->id);
                        if (! $record->hasRole($role)) {
                            $record->assignRole($role);
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Editar'),
                DetachAction::make()
                    ->label('Quitar del Equipo'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Quitar seleccionados'),
                ]),
            ]);
    }
}
