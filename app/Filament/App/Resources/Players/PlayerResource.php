<?php

namespace App\Filament\App\Resources\Players;

use App\Filament\App\Resources\Players\Pages\CreatePlayer;
use App\Filament\App\Resources\Players\Pages\EditPlayer;
use App\Filament\App\Resources\Players\Pages\ListPlayers;
use App\Filament\App\Resources\Players\Schemas\PlayerForm;
use App\Filament\App\Resources\Players\Tables\PlayersTable;
use App\Models\Player;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PlayerResource extends Resource
{
    protected static ?string $model = Player::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Jugadores';

    protected static ?string $modelLabel = 'Jugador';

    protected static ?string $pluralModelLabel = 'Jugadores';

    protected static ?string $tenantOwnershipRelationshipName = 'league';

    public static function form(Schema $schema): Schema
    {
        return PlayerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlayersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlayers::route('/'),
            'create' => CreatePlayer::route('/create'),
            'edit' => EditPlayer::route('/{record}/edit'),
        ];
    }
}
