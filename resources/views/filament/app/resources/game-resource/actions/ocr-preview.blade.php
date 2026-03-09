<div>
    <table class="w-full text-left divide-y divide-gray-200 dark:divide-white/5">
        <thead>
            <tr class="bg-gray-50 dark:bg-white/5">
                <th class="px-3 py-2 text-sm font-semibold">Jugador Detectado</th>
                <th class="px-3 py-2 text-sm font-semibold">Equipo</th>
                <th class="px-3 py-2 text-sm font-semibold text-center">VB</th>
                <th class="px-3 py-2 text-sm font-semibold text-center">H</th>
                <th class="px-3 py-2 text-sm font-semibold text-center">CI</th>
                <th class="px-3 py-2 text-sm font-semibold text-center">HR</th>
                <th class="px-3 py-2 text-sm font-semibold">Estado</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
            @foreach($results as $index => $result)
                <tr>
                    <td class="px-3 py-2 text-sm">
                        @if($result['player_id'])
                            <span class="text-success-600 font-medium">{{ $result['player_name'] }}</span>
                        @else
                            <span class="text-danger-600 font-medium">{{ $result['player_name'] }}</span>
                            <p class="text-xs text-gray-500">(No encontrado en sistema)</p>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-sm">
                        {{ $result['team_id'] == $game->home_team_id ? $game->homeTeam->name : $game->visitorTeam->name }}
                    </td>
                    <td class="px-3 py-2 text-sm text-center">{{ $result['stats']['ab'] ?? 0 }}</td>
                    <td class="px-3 py-2 text-sm text-center">{{ $result['stats']['h'] ?? 0 }}</td>
                    <td class="px-3 py-2 text-sm text-center">{{ $result['stats']['rbi'] ?? 0 }}</td>
                    <td class="px-3 py-2 text-sm text-center">{{ $result['stats']['hr'] ?? 0 }}</td>
                    <td class="px-3 py-2 text-sm">
                        @if($result['player_id'])
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-success-700 bg-success-50 rounded-md dark:bg-success-400/10 dark:text-success-400">Listo</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-warning-700 bg-warning-50 rounded-md dark:bg-warning-400/10 dark:text-warning-400">Omitir</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(collect($results)->whereNull('player_id')->isNotEmpty())
        <div class="mt-4 p-3 bg-warning-50 border-l-4 border-warning-400 text-warning-700 text-sm">
            <p><strong>Aviso:</strong> Algunos jugadores no pudieron ser vinculados automáticamente. Solo se cargarán las estadísticas de los jugadores en <strong>Verde</strong>.</p>
        </div>
    @endif
</div>
