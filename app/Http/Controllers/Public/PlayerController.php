<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function show(Player $player)
    {
        $player->load(['team', 'currentStats', 'stats.game.homeTeam', 'stats.game.visitorTeam']);

        $seoTitle = 'Analíticas: ' . $player->name . ' ' . $player->last_name;
        $seoDescription = 'Perfil oficial, stats y métricas de ' . $player->name . ' del equipo ' . ($player->team ? $player->team->name : 'Agente Libre');
        return view('public.player.show', compact('player', 'seoTitle', 'seoDescription'));
    }
}