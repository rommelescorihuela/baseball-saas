<?php

namespace App\Services;

use App\Models\PlayerGameStat;
use App\Models\PlayerSeasonStat;
use App\Models\Season;
use Illuminate\Support\Facades\DB;

class SeasonStatsAggregator
{
    /**
     * Agrega las estadísticas de todos los juegos de una temporada
     * a las estadísticas acumuladas de la temporada.
     */
    public function aggregateSeason(Season $season): void
    {
        // Obtener todos los juegos finalizados de la temporada
        $gameIds = $season->competitions()
            ->with('games')
            ->get()
            ->pluck('games')
            ->flatten()
            ->where('status', 'finished')
            ->pluck('id');

        // Obtener todas las estadísticas de juegos de la temporada
        $gameStats = PlayerGameStat::whereIn('game_id', $gameIds)
            ->select([
                'player_id',
                'team_id',
                DB::raw('COUNT(*) as g'),
                DB::raw('SUM(ab) as ab'),
                DB::raw('SUM(h) as h'),
                DB::raw('SUM("1b") as "1b"'),
                DB::raw('SUM("2b") as "2b"'),
                DB::raw('SUM("3b") as "3b"'),
                DB::raw('SUM(hr) as hr'),
                DB::raw('SUM(r) as r'),
                DB::raw('SUM(rbi) as rbi'),
                DB::raw('SUM(bb) as bb'),
                DB::raw('SUM(so) as so'),
                DB::raw('SUM(hbp) as hbp'),
                DB::raw('SUM(sb) as sb'),
                DB::raw('SUM(cs) as cs'),
                DB::raw('SUM(sac) as sac'),
                DB::raw('SUM(sf) as sf'),
                // Pitching stats
                DB::raw('SUM(ip) as ip'),
                DB::raw('SUM(er) as er'),
                DB::raw('SUM(p_h) as p_h'),
                DB::raw('SUM(p_r) as p_r'),
                DB::raw('SUM(p_bb) as p_bb'),
                DB::raw('SUM(p_so) as p_so'),
                DB::raw('SUM(p_hr) as p_hr'),
                DB::raw('SUM(w) as w'),
                DB::raw('SUM(l) as l'),
                DB::raw('SUM(sv) as sv'),
            ])
            ->groupBy('player_id', 'team_id')
            ->get();

        foreach ($gameStats as $stats) {
            PlayerSeasonStat::updateOrCreate(
                [
                    'season_id' => $season->id,
                    'player_id' => $stats->player_id,
                    'team_id' => $stats->team_id,
                ],
                [
                    'g' => $stats->g,
                    'ab' => $stats->ab,
                    'h' => $stats->h,
                    '1b' => $stats->{'1b'},
                    '2b' => $stats->{'2b'},
                    '3b' => $stats->{'3b'},
                    'hr' => $stats->hr,
                    'r' => $stats->r,
                    'rbi' => $stats->rbi,
                    'bb' => $stats->bb,
                    'so' => $stats->so,
                    'hbp' => $stats->hbp,
                    'sb' => $stats->sb,
                    'cs' => $stats->cs,
                    'sac' => $stats->sac,
                    'sf' => $stats->sf,
                    'ip' => $stats->ip,
                    'er' => $stats->er,
                    'p_h' => $stats->p_h,
                    'p_r' => $stats->p_r,
                    'p_bb' => $stats->p_bb,
                    'p_so' => $stats->p_so,
                    'p_hr' => $stats->p_hr,
                    'w' => $stats->w,
                    'l' => $stats->l,
                    'sv' => $stats->sv,
                ]
            );
        }
    }

    /**
     * Agrega las estadísticas de un jugador específico para una temporada
     */
    public function aggregatePlayerSeason(Season $season, int $playerId, int $teamId): void
    {
        // Obtener todos los juegos finalizados de la temporada
        $gameIds = $season->competitions()
            ->with('games')
            ->get()
            ->pluck('games')
            ->flatten()
            ->where('status', 'finished')
            ->pluck('id');

        // Obtener estadísticas del jugador
        $stats = PlayerGameStat::whereIn('game_id', $gameIds)
            ->where('player_id', $playerId)
            ->where('team_id', $teamId)
            ->select([
                DB::raw('COUNT(*) as g'),
                DB::raw('SUM(ab) as ab'),
                DB::raw('SUM(h) as h'),
                DB::raw('SUM("1b") as "1b"'),
                DB::raw('SUM("2b") as "2b"'),
                DB::raw('SUM("3b") as "3b"'),
                DB::raw('SUM(hr) as hr'),
                DB::raw('SUM(r) as r'),
                DB::raw('SUM(rbi) as rbi'),
                DB::raw('SUM(bb) as bb'),
                DB::raw('SUM(so) as so'),
                DB::raw('SUM(hbp) as hbp'),
                DB::raw('SUM(sb) as sb'),
                DB::raw('SUM(cs) as cs'),
                DB::raw('SUM(sac) as sac'),
                DB::raw('SUM(sf) as sf'),
                DB::raw('SUM(ip) as ip'),
                DB::raw('SUM(er) as er'),
                DB::raw('SUM(p_h) as p_h'),
                DB::raw('SUM(p_r) as p_r'),
                DB::raw('SUM(p_bb) as p_bb'),
                DB::raw('SUM(p_so) as p_so'),
                DB::raw('SUM(p_hr) as p_hr'),
                DB::raw('SUM(w) as w'),
                DB::raw('SUM(l) as l'),
                DB::raw('SUM(sv) as sv'),
            ])
            ->first();

        if ($stats && $stats->g > 0) {
            PlayerSeasonStat::updateOrCreate(
                [
                    'season_id' => $season->id,
                    'player_id' => $playerId,
                    'team_id' => $teamId,
                ],
                [
                    'g' => $stats->g,
                    'ab' => $stats->ab,
                    'h' => $stats->h,
                    '1b' => $stats->{'1b'},
                    '2b' => $stats->{'2b'},
                    '3b' => $stats->{'3b'},
                    'hr' => $stats->hr,
                    'r' => $stats->r,
                    'rbi' => $stats->rbi,
                    'bb' => $stats->bb,
                    'so' => $stats->so,
                    'hbp' => $stats->hbp,
                    'sb' => $stats->sb,
                    'cs' => $stats->cs,
                    'sac' => $stats->sac,
                    'sf' => $stats->sf,
                    'ip' => $stats->ip,
                    'er' => $stats->er,
                    'p_h' => $stats->p_h,
                    'p_r' => $stats->p_r,
                    'p_bb' => $stats->p_bb,
                    'p_so' => $stats->p_so,
                    'p_hr' => $stats->p_hr,
                    'w' => $stats->w,
                    'l' => $stats->l,
                    'sv' => $stats->sv,
                ]
            );
        }
    }
}
