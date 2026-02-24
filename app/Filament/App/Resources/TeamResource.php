<?php

namespace App\Filament\App\Resources;

use App\Models\Team;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $tenantOwnershipRelationshipName = 'league';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Detalles del Equipo')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Nombre del Equipo')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('teams/logos'),
                    ]),
                \Filament\Schemas\Components\Section::make('Encargado del Equipo (Manager)')
                    ->description('Se enviará una invitación a este correo.')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('owner_name')
                            ->label('Nombre del Encargado')
                            ->required(fn (string $operation) => $operation === 'create')
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('owner_email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required(fn (string $operation) => $operation === 'create')
                            ->maxLength(255),
                    ])
                    ->visible(fn (string $operation) => $operation === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('logo'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\App\Resources\TeamResource\RelationManagers\UsersRelationManager::class,
            \App\Filament\App\Resources\TeamResource\RelationManagers\SeasonRosterRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\App\Resources\TeamResource\Pages\ListTeams::route('/'),
            'create' => \App\Filament\App\Resources\TeamResource\Pages\CreateTeam::route('/create'),
            'edit' => \App\Filament\App\Resources\TeamResource\Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
