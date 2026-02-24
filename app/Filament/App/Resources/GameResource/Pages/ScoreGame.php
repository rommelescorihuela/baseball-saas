<?php

namespace App\Filament\App\Resources\GameResource\Pages;

use App\Filament\App\Resources\GameResource;
use App\Models\Game;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;

class ScoreGame extends Page
{
    protected static string $resource = GameResource::class;

    protected string $view = 'filament.app.resources.game-resource.pages.score-game';

    public Game $record;

    public function mount(int|string $record): void
    {
        $this->record = Game::findOrFail($record);
    }

    public function getTitle(): string
    {
        return "Scoring: " . $this->record->homeTeam->name . " vs " . $this->record->visitorTeam->name;
    }
}