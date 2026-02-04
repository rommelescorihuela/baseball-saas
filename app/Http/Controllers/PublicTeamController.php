<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PublicTeamController extends Controller
{
    public function index()
    {
        $team = app('currentTeam');

        // Cargar jugadores con sus relaciones de manera eficiente
        $team->load(['players']);

        $roster = $team->players()->take(10)->get();

        return view('teams.dashboard', [
            'team' => $team,
            'roster' => $roster
        ]);
    }

    public function showPlayer(Request $request)
    {
        // Obtener el ID del jugador directamente de la ruta
        $playerId = $request->route('id');
        
        Log::debug('showPlayer called with playerId: ' . $playerId);
        Log::debug('Current team: ' . (app('currentTeam') ? app('currentTeam')->id : 'null'));
        Log::debug('All route parameters: ', $request->route()->parameters());
        
        if (!$playerId) {
            abort(404, 'Player ID not found in route');
        }

        // Buscar el jugador por ID
        $player = Player::findOrFail($playerId);

        // Asegurar que el jugador pertenece al equipo actual
        if ($player->team_id !== app('currentTeam')->id) {
            abort(404);
        }

        // Eager loading stats efficiently
        $player->load([
            'stats' => function ($query) {
                $query->latest();
            }
        ]);

        return view('players.show', [
            'player' => $player,
            'team' => app('currentTeam'),
            'stats' => $player->stats
        ]);
    }
}
