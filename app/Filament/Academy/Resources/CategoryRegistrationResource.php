<?php

namespace App\Filament\Academy\Resources;

use App\Models\Category;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

class CategoryRegistrationResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Inscribirse en Ligas';

    protected static ?string $modelLabel = 'Liga/Categoría';

    protected static ?string $pluralModelLabel = 'Ligas Disponibles';

    // Este recurso no es propiedad del tenant directamente (mostramos todas las categorías del sistema)
    protected static bool $isScopedToTenant = false;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('league.name')
                    ->label('Liga')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Categoría / Torneo')
                    ->searchable(),
                TextColumn::make('teams_count')
                    ->label('Equipos')
                    ->counts('teams'),
                TextColumn::make('status')
                    ->label('Estado de Mi Inscripción')
                    ->state(function (Category $record) {
                        $team = Filament::getTenant();
                        $pivot = $record->teams()->where('team_id', $team->id)->first()?->pivot;

                        if (!$pivot)
                            return 'No inscrito';

                        return match ($pivot->status) {
                            'pending' => 'Pendiente',
                            'approved' => 'Aprobado',
                            'rejected' => 'Rechazado',
                            default => 'Desconocido',
                        };
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Aprobado' => 'success',
                        'Pendiente' => 'warning',
                        'Rechazado' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                \Filament\Actions\Action::make('register')
                    ->label('Solicitar Inscripción')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(function (Category $record) {
                        $team = Filament::getTenant();
                        if (!$team)
                            return false;
                        return !$record->teams()->where('team_id', $team->id)->exists();
                    })
                    ->action(function (Category $record) {
                        $team = Filament::getTenant();
                        $record->teams()->attach($team->id, ['status' => 'pending']);

                        \Filament\Notifications\Notification::make()
                            ->title('Solicitud enviada')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Academy\Resources\CategoryRegistrationResource\Pages\ListCategoryRegistrations::route('/'),
        ];
    }
}
