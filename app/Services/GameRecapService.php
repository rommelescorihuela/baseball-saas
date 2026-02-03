<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Article;
use App\Models\PlayerStat;
use Illuminate\Support\Str;

class GameRecapService
{
    public function generateRecap(Game $game): ?Article
    {
        // Skip if scores are missing
        if (is_null($game->home_score) || is_null($game->away_score)) {
            return null;
        }

        // Determine Winner
        if ($game->home_score > $game->away_score) {
            $winner = $game->homeTeam;
            $loser = $game->awayTeam;
            $winnerScore = $game->home_score;
            $loserScore = $game->away_score;
        } else {
            $winner = $game->awayTeam;
            $loser = $game->homeTeam;
            $winnerScore = $game->away_score;
            $loserScore = $game->home_score;
        }

        if ($game->home_score === $game->away_score) {
            return null; // Draws might not generate exciting recaps yet
        }

        // Find MVP (Player with most RBIs + Hits + Runs in the winning team)
        $mvpStat = PlayerStat::where('game_id', $game->id)
            ->whereHas('player', function ($q) use ($winner) {
                $q->where('team_id', $winner->id);
            })
            ->orderByRaw('(hits + rbis + runs + home_runs * 2) DESC')
            ->with('player')
            ->first();

        $mvp = $mvpStat ? $mvpStat->player : null;

        // Generate Title
        $verbs = ['defeats', 'crushes', 'edges out', 'overcomes', 'beats'];
        $verb = $verbs[array_rand($verbs)];

        $diff = $winnerScore - $loserScore;
        if ($diff > 5)
            $verb = 'dominates';
        if ($diff == 1)
            $verb = 'edges out';

        $title = "{$winner->name} {$verb} {$loser->name} {$winnerScore}-{$loserScore}";

        // Generate Content
        $content = "<p>In an exciting matchup, the <strong>{$winner->name}</strong> secured a victory against the <strong>{$loser->name}</strong> with a final score of {$winnerScore} to {$loserScore}.</p>";

        if ($mvp) {
            $content .= "<p><strong>{$mvp->first_name} {$mvp->last_name}</strong> was the standout player of the game, helping lead the {$winner->name} to victory.</p>";
            if ($mvpStat->home_runs > 0) {
                $content .= "<p>It was a spectacular display of power, including {$mvpStat->home_runs} home run(s).</p>";
            }
        }

        $content .= "<p>The {$winner->name} will look to carry this momentum into their next game.</p>";

        // Create Article
        // Use a generic placeholder representing the sport or specific team color if available
        $image = "https://placehold.co/800x400/0f172a/FFF?text={$winner->name}+vs+{$loser->name}";

        return Article::create([
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $game->id,
            'content' => $content,
            'category' => 'Recap',
            'image_url' => $image,
            'published_at' => $game->game_date ?? now(),
        ]);
    }
}
