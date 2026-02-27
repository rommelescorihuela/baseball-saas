<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime('d M Y, h:i A')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')->label('Customer')->searchable()->sortable(),
                TextColumn::make('plan.name')->label('Plan')->badge(),
                TextColumn::make('payment_method')->badge()->color('gray'),
                TextColumn::make('reference_number')->searchable()->copyable(),
                TextColumn::make('amount')->money(fn($record) => $record->currency)->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors(['warning' => 'pending', 'success' => 'approved', 'danger' => 'rejected']),
                ImageColumn::make('receipt_path')->circular()->label('Receipt'),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ]),
                SelectFilter::make('payment_method')->options([
                    'pago_movil' => 'Pago Móvil (VE)',
                    'zelle' => 'Zelle',
                    'cash' => 'Efectivo',
                ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn(Model $record) => $record->status === 'approved')
                    ->action(function (Model $record) {
                        $record->update(['status' => 'approved', 'verified_at' => now()]);
                        // Logica de crear/actualizar suscripción
                        $subscription = \App\Models\Subscription::updateOrCreate(
                            ['user_id' => $record->user_id, 'plan_id' => $record->plan_id, 'status' => 'active'],
                            [
                                'starts_at' => now(),
                                'expires_at' => now()->addDays($record->plan->duration_days)
                            ]
                        );
                        $record->update(['subscription_id' => $subscription->id]);
                        Notification::make()->title('Payment Approved & Plan Active!')->success()->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
    }
}
