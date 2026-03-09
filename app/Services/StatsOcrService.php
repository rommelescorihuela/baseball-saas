<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Player;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class StatsOcrService
{
    protected string $apiKey;
    protected bool $enabled;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', '');
        $this->enabled = config('services.gemini.enabled', false);
    }

    /**
     * Process an image of a box score and return structured stats.
     */
    public function processBoxScore(string $imagePath, Game $game): array
    {
        if (!$this->enabled || empty($this->apiKey)) {
            throw new \Exception('El servicio de OCR de estadísticas no está configurado o habilitado.');
        }

        $imageData = base64_encode(file_get_contents($imagePath));
        $mimeType = mime_content_type($imagePath);

        $prompt = $this->getPrompt($game);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$this->apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $imageData,
                            ],
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
            ],
        ]);

        if (!$response->successful()) {
            Log::error('Error calling Gemini API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Error al procesar la imagen con la IA.');
        }

        $result = $response->json();
        $textResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
        
        $parsedStats = json_decode($textResponse, true) ?? [];
        
        return $this->matchPlayersWithStats($parsedStats, $game);
    }

    protected function getPrompt(Game $game): string
    {
        $homeTeamPlayers = $game->homeTeam->players->pluck('name')->implode(', ');
        $visitorTeamPlayers = $game->visitorTeam->players->pluck('name')->implode(', ');

        return <<<PROMPT
Analiza esta imagen de una hoja de anotaciones de béisbol (box score). 
Extrae las estadísticas de los jugadores en un formato JSON estructurado.

Los equipos que jugaron son:
- {$game->homeTeam->name} (Home)
- {$game->visitorTeam->name} (Visitor)

Jugadores registrados en {$game->homeTeam->name}: {$homeTeamPlayers}
Jugadores registrados en {$game->visitorTeam->name}: {$visitorTeamPlayers}

Debes devolver un array de objetos, donde cada objeto represente un jugador y sus estadísticas detectadas.
Usa las siguientes claves para las estadísticas:
- "player_name": Nombre tal cual aparece en la imagen.
- "team_side": "home" o "visitor".
- "ab": Veces al bate (at bats).
- "r": Carreras (runs).
- "h": Hits.
- "rbi": Carreras impulsadas (runs batted in).
- "doubles": Dobles.
- "triples": Triples.
- "hr": Home runs.
- "bb": Base por bolas.
- "so": Ponches (strikeouts).
- "sb": Bases robadas.

Si detectas estadísticas de pitcheo:
- "ip": Entradas lanzadas (innings pitched).
- "p_h": Hits permitidos.
- "p_r": Carreras permitidas.
- "p_er": Carreras limpias permitidas.
- "p_bb": Boletos permitidos.
- "p_so": Ponches propinados.

IMPORTANTE: 
1. Intenta emparejar el "player_name" con los nombres de los jugadores registrados sugeridos arriba.
2. Si un jugador no está en la lista, usa el nombre que leas en la imagen.
3. Devuelve SOLO el JSON.
PROMPT;
    }

    protected function matchPlayersWithStats(array $stats, Game $game): array
    {
        $allPlayers = $game->homeTeam->players->merge($game->visitorTeam->players);
        
        return array_map(function ($stat) use ($allPlayers, $game) {
            $matchedPlayer = $this->findBestMatch($stat['player_name'] ?? '', $allPlayers);
            
            return [
                'player_id' => $matchedPlayer?->id,
                'player_name' => $matchedPlayer?->name ?? ($stat['player_name'] ?? 'Desconocido'),
                'team_id' => $this->getTeamId($stat, $game, $matchedPlayer),
                'stats' => array_diff_key($stat, array_flip(['player_name', 'team_side'])),
            ];
        }, $stats);
    }

    protected function findBestMatch(string $name, $players): ?Player
    {
        if (empty($name)) return null;

        // Búsqueda exacta
        $exact = $players->first(fn($p) => strtolower($p->name) === strtolower($name));
        if ($exact) return $exact;

        // Búsqueda por similitud (muy básica por ahora)
        return $players->sortByDesc(fn($p) => levenshtein(strtolower($p->name), strtolower($name)))
            ->first();
    }

    protected function getTeamId(array $stat, Game $game, ?Player $player): int
    {
        if ($player) {
            // Intentar determinar por el equipo del jugador registrado en este contexto
            // Aquí asumimos que el jugador pertenece a uno de los dos equipos del juego
            $isHome = $game->homeTeam->players->contains('id', $player->id);
            return $isHome ? $game->home_team_id : $game->visitor_team_id;
        }

        $side = strtolower($stat['team_side'] ?? '');
        return ($side === 'visitor') ? $game->visitor_team_id : $game->home_team_id;
    }
}
