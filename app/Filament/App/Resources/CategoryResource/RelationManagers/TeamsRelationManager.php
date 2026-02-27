<?php

namespace App\Filament\App\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TeamsRelationManager extends RelationManager
{
    protected static string $relationship = 'teams';

    protected static ?string $title = 'Equipos Inscritos / Solicitudes';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre del Equipo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                        default => $state,
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // No habilitar creación desde aquí para forzar el flujo de solicitud
            ])
            ->actions([
                \Filament\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Model $record) => $record->pivot->status === 'pending')
                    ->action(function (Model $record, RelationManager $livewire) {
                        $league = \Filament\Facades\Filament::getTenant();

                        if (!$league->canApproveTeam()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Límite de plan alcanzado')
                                ->body('Tu plan actual no permite más equipos. Mejora tu suscripción.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->pivot->status = 'approved';
                        $record->pivot->save();

                        \Filament\Notifications\Notification::make()
                            ->title('Equipo aprobado')
                            ->success()
                            ->send();
                    }),
                \Filament\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Model $record) => $record->pivot->status === 'pending')
                    ->action(function (Model $record) {
                        $record->pivot->status = 'rejected';
                        $record->pivot->save();

                        \Filament\Notifications\Notification::make()
                            ->title('Solicitud rechazada')
                            ->danger()
                            ->send();
                    }),
                \Filament\Actions\DetachAction::make()
                    ->label('Quitar de Categoría'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
