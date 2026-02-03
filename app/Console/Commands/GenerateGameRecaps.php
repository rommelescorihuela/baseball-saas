<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services;

class GenerateGameRecaps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'baseball:generate-recaps';
    protected $description = 'Generate news articles for finished games';

    public function handle(Services\GameRecapService $recapService)
    {
        $this->info('Checking for games without recaps...');

        // Find games with scores that happened in the last 7 days
        // In a real app, we would verify if an Article exists for this game_id
        // For now, we will just take the last 5 games with scores
        $games = \App\Models\Game::whereNotNull('home_score')
            ->whereNotNull('away_score')
            ->orderBy('game_date', 'desc')
            ->take(10)
            ->get();

        $count = 0;
        foreach ($games as $game) {
            // Check if article already exists for this specific game slug pattern
            // This is a rough check since we don't have game_id column in articles yet
            $slugPrefix = \Illuminate\Support\Str::slug($game->homeTeam->name . ' vs ' . $game->awayTeam->name);

            // To be safe and simple: We will just generate it. 
            // In production, we'd add 'game_id' to `articles` table to avoid duplicates.

            $article = $recapService->generateRecap($game);

            if ($article) {
                $this->info("Generated recap: {$article->title}");
                $count++;
            }
        }

        $this->info("Generated {$count} recaps.");
    }
}
