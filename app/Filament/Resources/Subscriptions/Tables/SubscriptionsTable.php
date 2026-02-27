<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Customer')->searchable()->sortable(),
                TextColumn::make('plan.name')->badge()->color('primary')->sortable(),
                TextColumn::make('starts_at')->date()->sortable(),
                TextColumn::make('expires_at')->date()->sortable()
                    ->color(fn($state) => $state->isPast() ? 'danger' : 'success'),
                TextColumn::make('status')
                    ->badge()
                    ->colors(['success' => 'active', 'danger' => 'expired', 'warning' => 'pending']),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'pending' => 'Pending',
                    ]),
                SelectFilter::make('plan_id')
                    ->relationship('plan', 'name')
                    ->label('Filter by Plan'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
