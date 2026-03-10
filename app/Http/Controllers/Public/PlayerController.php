<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function show(Player $player)
    {
        $player->load(['team', 'currentStats', 'gameStats.game.homeTeam', 'gameStats.game.visitorTeam', 'stats.season']);

        // 1. Determinar el Rol Principal
        $isPitcher = in_array($player->position, ['P', 'LHP', 'RHP', 'RP', 'SP']);

        // 2. Extraer Game Log (Últimos 10 Juegos)
        $gameLog = $player->gameStats()->with(['game.homeTeam', 'game.visitorTeam'])->get()->sortByDesc(fn($stat) => $stat->game->start_time)->take(10);

        // 3. Extraer Career Stats (Agrupados por Temporada)
        $careerStats = $player->stats->groupBy('season_id')->map(function ($seasonStats) {
            $firstStat = $seasonStats->first();
            
            // Calculos acumulados para bateo
            $ab = $seasonStats->sum('ab');
            $h = $seasonStats->sum('h');
            $doubles = $seasonStats->sum('doubles');
            $triples = $seasonStats->sum('triples');
            $hr = $seasonStats->sum('hr');
            $bb = $seasonStats->sum('bb');
            $r = $seasonStats->sum('r');
            $rbi = $seasonStats->sum('rbi');
            $sb = $seasonStats->sum('sb');
            $so = $seasonStats->sum('so');
            
            $avg = $ab > 0 ? number_format($h / $ab, 3) : '.000';
            $obp = ($ab + $bb) > 0 ? number_format(($h + $bb) / ($ab + $bb), 3) : '.000';
            $tb = $h + $doubles + ($triples * 2) + ($hr * 3); // Total Bases
            $slg = $ab > 0 ? number_format($tb / $ab, 3) : '.000';
            $ops = ($obp !== '.000' && $slg !== '.000') ? number_format((float)$obp + (float)$slg, 3) : '.000';

            // Calculos acumulados para pitcheo
            $ip = $seasonStats->sum('ip');
            $er = $seasonStats->sum('p_er');
            $w = $seasonStats->sum('w');
            $l = $seasonStats->sum('l');
            $sv = $seasonStats->sum('sv');
            $p_h = $seasonStats->sum('p_h');
            $p_bb = $seasonStats->sum('p_bb');
            $p_so = $seasonStats->sum('p_so');
            
            $era = $ip > 0 ? number_format(($er * 9) / $ip, 2) : '0.00';
            $whip = $ip > 0 ? number_format(($p_h + $p_bb) / $ip, 2) : '0.00';

            return [
                'season_name' => $firstStat->season ? $firstStat->season->name : 'N/A',
                'g' => $seasonStats->count(),
                // Batting
                'ab' => $ab, 'h' => $h, '2b' => $doubles, '3b' => $triples, 'hr' => $hr, 
                'r' => $r, 'rbi' => $rbi, 'bb' => $bb, 'so' => $so, 'sb' => $sb,
                'avg' => str_replace('0.', '.', $avg), 'obp' => str_replace('0.', '.', $obp), 
                'slg' => str_replace('0.', '.', $slg), 'ops' => str_replace('0.', '.', $ops),
                // Pitching
                'ip' => $ip, 'er' => $er, 'w' => $w, 'l' => $l, 'sv' => $sv,
                'p_h' => $p_h, 'p_bb' => $p_bb, 'p_so' => $p_so,
                'era' => $era, 'whip' => $whip
            ];
        });

        // 4. Calcular Scouting Score (Radar Chart 1-100)
        // Valores Normalizados solo para demostración visual
        if ($isPitcher) {
            $totalIp = $player->stats->sum('ip');
            $totalPSo = $player->stats->sum('p_so');
            $totalPBb = $player->stats->sum('p_bb');
            
            // Ejemplo de normalización simple (K/9, BB/9, etc)
            $k9 = $totalIp > 0 ? ($totalPSo * 9) / $totalIp : 0;
            $bb9 = $totalIp > 0 ? ($totalPBb * 9) / $totalIp : 0;
            
            $scouting = [
                'Velocity' => min(100, 60 + ($k9 * 3)), // Factor simulado
                'Control' => min(100, max(20, 100 - ($bb9 * 8))),
                'Stamina' => min(100, 40 + ($totalIp * 2)),
                'Breaking' => rand(60, 95), // Dato que idealmente sale de scouts
                'Mechanics' => rand(70, 90)
            ];
        } else {
            $totalAb = $player->stats->sum('ab');
            $totalH = $player->stats->sum('h');
            $totalHr = $player->stats->sum('hr');
            $totalSb = $player->stats->sum('sb');
            
            $avgValue = $totalAb > 0 ? ($totalH / $totalAb) : 0;
            $hrRatio = $totalAb > 0 ? ($totalHr / $totalAb) : 0;
            
            $scouting = [
                'Contact' => min(100, $avgValue * 250), // .300 = 75
                'Power' => min(100, 40 + ($hrRatio * 600)), 
                'Speed' => min(100, 50 + ($totalSb * 5)),
                'Fielding' => rand(65, 95), // Idealmente se extrae de E (Errores) pero no se llevan detallados aún
                'Arm' => rand(70, 90)
            ];
        }

        $seoTitle = 'Analíticas: ' . $player->name . ' ' . $player->last_name;
        $seoDescription = 'Perfil oficial, stats y métricas de ' . $player->name . ' del equipo ' . ($player->team ? $player->team->name : 'Agente Libre');
        
        return view('public.player.show', compact('player', 'seoTitle', 'seoDescription', 'isPitcher', 'gameLog', 'careerStats', 'scouting'));
    }
}