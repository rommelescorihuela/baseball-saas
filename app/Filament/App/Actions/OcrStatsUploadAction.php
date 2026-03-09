<?php

namespace App\Filament\App\Actions;

use App\Services\StatsOcrService;
use App\Models\PlayerGameStat;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class OcrStatsUploadAction
{
    public static function make(): Action
    {
        return Action::make('ocr_upload')
            ->label('Cargar desde Foto')
            ->icon('heroicon-o-camera')
            ->color('warning')
            ->visible(fn() => config('services.gemini.enabled'))
            ->form([
                FileUpload::make('photo')
                    ->label('Foto de la Hoja de Anotación')
                    ->image()
                    ->required()
                    ->disk('public')
                    ->directory('ocr-stats'),
            ])
            ->action(function (array $data, $livewire): void {
                $game = $livewire->getOwnerRecord();
                $service = app(StatsOcrService::class);
                
                try {
                    $path = Storage::disk('public')->path($data['photo']);
                    $results = $service->processBoxScore($path, $game);
                    
                    $savedCount = 0;
                    foreach ($results as $result) {
                        if (empty($result['player_id'])) continue;

                        PlayerGameStat::updateOrCreate(
                            [
                                'game_id' => $game->id,
                                'player_id' => $result['player_id'],
                            ],
                            array_merge($result['stats'], [
                                'team_id' => $result['team_id'],
                            ])
                        );
                        $savedCount++;
                    }
                    
                    Notification::make()
                        ->title('Estadísticas Cargadas')
                        ->body("Se han procesado correctamente {$savedCount} jugadores de la foto.")
                        ->success()
                        ->send();
                        
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Error procesando imagen')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
