<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function show(Player $player)
    {
        $player->load(['team', 'stats.game.homeTeam', 'stats.game.visitorTeam']);

        return view('public.player.show', compact('player'));
    }
}