<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

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

    public function showPlayer(Player $player)
    {
        // Asegurar que el jugador pertenece al equipo actual
        if ($player->team_id !== app('currentTeam')->id) {
            abort(404);
        }

        // Eager loading stats efficiently
        $player->load([
            'stats' => function ($query) {
                $query->latest(); // Asumimos created_at o añadiremos game_date
            }
        ]);

        // Calcular totales de la temporada (Simulado por ahora ya que requeriría lógica de agregación compleja)
        // En un escenario real, haríamos $player->stats->sum('hits') etc.

        return view('players.show', [
            'player' => $player,
            'team' => app('currentTeam'),
            'stats' => $player->stats
        ]);
    }
}
