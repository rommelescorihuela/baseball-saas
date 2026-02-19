<?php

namespace App\Filament\Resources\Leagues\Schemas;

use Filament\Schemas\Schema;

class LeagueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Detalles de la Liga')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Nombre de la Liga')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),

                        \Filament\Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Activa',
                                'inactive' => 'Inactiva',
                                'suspended' => 'Suspendida',
                            ])
                            ->default('active')
                            ->required(),

                        \Filament\Forms\Components\Select::make('plan')
                            ->options(\App\Enums\Plan::class)
                            ->default(\App\Enums\Plan::FREE)
                            ->required(),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Administrador de la Liga (Owner)')
                    ->description('Se creará un usuario con estos datos y se le enviarán las credenciales.')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('owner_name')
                            ->label('Nombre del Admin')
                            ->required(fn (string $operation) => $operation === 'create')
                            ->maxLength(255),

                        \Filament\Forms\Components\TextInput::make('owner_email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required(fn (string $operation) => $operation === 'create')
                            ->maxLength(255)
                            ->unique('users', 'email', ignoreRecord: true),

                        \Filament\Forms\Components\TextInput::make('owner_password')
                            ->label('Contraseña Inicial')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation) => $operation === 'create')
                            ->minLength(8),
                    ])->columns(2)
                    ->visible(fn (string $operation) => $operation === 'create'),
            ]);
    }
}
