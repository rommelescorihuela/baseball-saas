<?php

namespace App\Filament\App\Resources\GameResource\Pages;

use App\Filament\App\Resources\GameResource;
use App\Models\Game;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class ViewBoxScore extends Page
{
    use InteractsWithRecord;

    protected static string $resource = GameResource::class;

    protected string $view = 'filament.app.resources.game-resource.pages.view-box-score';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getVisitorStatsProperty()
    {
        return $this->record->stats()->where('team_id', $this->record->visitor_team_id)->with('player')->get();
    }

    public function getHomeStatsProperty()
    {
        return $this->record->stats()->where('team_id', $this->record->home_team_id)->with('player')->get();
    }
}