<x-layouts.public>
    <div class="relative min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        {{-- Background Decorations --}}
        <div class="absolute top-0 right-0 -z-10 size-[500px] bg-primary/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-0 left-0 -z-10 size-[300px] bg-accent/5 blur-[100px] rounded-full"></div>

        <div class="max-w-7xl mx-auto space-y-8">
            {{-- Hero Section --}}
            <div class="relative">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-3 mb-2">
                             <div class="size-16 rounded-2xl bg-primary/20 border border-primary/30 flex items-center justify-center text-primary shadow-[0_0_30px_rgba(255,110,64,0.3)]">
                                @if($team->logo)
                                    <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}" class="size-12 object-contain" />
                                @else
                                    <span class="material-symbols-outlined text-4xl font-bold">groups</span>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-widest text-primary italic">Academy Profile</h4>
                                <h1 class="text-4xl md:text-5xl font-display font-black tracking-tight text-white uppercase italic">
                                    {{ $team->name }}
                                </h1>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 text-slate-400">
                            <span class="flex items-center gap-1 text-sm font-bold uppercase tracking-wider">
                                <span class="material-symbols-outlined text-sm">location_on</span>
                                {{ $team->city ?? 'Sede Principal' }}
                            </span>
                            <span class="flex items-center gap-1 text-sm font-bold uppercase tracking-wider text-slate-300">
                                <span class="material-symbols-outlined text-sm">sports_score</span>
                                Record: <span class="text-white">{{ $seasonRecord }}</span>
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @foreach($team->categories->map(fn($cat) => $cat->league)->unique('id') as $league)
                            <div class="glass px-4 py-2 rounded-xl flex items-center gap-2 border-l-2 border-primary">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-200">{{ $league->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Líderes del Equipo (Leaderboards) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Líderes de Bateo -->
                <div class="glass p-6 rounded-2xl border border-white/5 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <h2 class="text-sm font-black uppercase tracking-[0.2em] text-primary italic flex items-center gap-2 mb-6">
                        <span class="material-symbols-outlined text-lg">sports_baseball</span>
                        Líderes de Bateo
                    </h2>
                    <div class="grid grid-cols-3 gap-4">
                        {{-- AVG --}}
                        <div class="flex flex-col items-center text-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">AVG</span>
                            @if($leadersBatting['avg']->isNotEmpty())
                                @php $p = $leadersBatting['avg']->first(); @endphp
                                <div class="size-12 rounded-full bg-slate-800 border border-primary/30 flex items-center justify-center font-black text-white mb-2 shadow-[0_0_15px_rgba(255,110,64,0.15)]">{{ $p->number ?? '00' }}</div>
                                <span class="text-xs font-bold text-slate-200 truncate w-full">{{ $p->name }} {{ $p->last_name }}</span>
                                <span class="text-lg font-black text-primary italic">{{ $p->currentStats?->avg ?? '.000' }}</span>
                            @else
                                <span class="text-xs text-slate-600">N/A</span>
                            @endif
                        </div>
                        {{-- HR --}}
                        <div class="flex flex-col items-center text-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">HR</span>
                            @if($leadersBatting['hr']->isNotEmpty())
                                @php $p = $leadersBatting['hr']->first(); @endphp
                                <div class="size-12 rounded-full bg-slate-800 border border-white/5 flex items-center justify-center font-black text-white mb-2">{{ $p->number ?? '00' }}</div>
                                <span class="text-xs font-bold text-slate-200 truncate w-full">{{ $p->name }} {{ $p->last_name }}</span>
                                <span class="text-lg font-black text-white italic">{{ $p->currentStats?->hr ?? 0 }}</span>
                            @else
                                <span class="text-xs text-slate-600">N/A</span>
                            @endif
                        </div>
                        {{-- RBI --}}
                        <div class="flex flex-col items-center text-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">CI</span>
                            @if($leadersBatting['rbi']->isNotEmpty())
                                @php $p = $leadersBatting['rbi']->first(); @endphp
                                <div class="size-12 rounded-full bg-slate-800 border border-white/5 flex items-center justify-center font-black text-white mb-2">{{ $p->number ?? '00' }}</div>
                                <span class="text-xs font-bold text-slate-200 truncate w-full">{{ $p->name }} {{ $p->last_name }}</span>
                                <span class="text-lg font-black text-white italic">{{ $p->currentStats?->rbi ?? 0 }}</span>
                            @else
                                <span class="text-xs text-slate-600">N/A</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Líderes de Pitcheo -->
                <div class="glass p-6 rounded-2xl border border-white/5 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-accent/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <h2 class="text-sm font-black uppercase tracking-[0.2em] text-accent italic flex items-center gap-2 mb-6">
                        <span class="material-symbols-outlined text-lg">sports_cricket</span>
                        Líderes de Pitcheo
                    </h2>
                    <div class="grid grid-cols-3 gap-4">
                        {{-- ERA --}}
                        <div class="flex flex-col items-center text-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">ERA</span>
                            @if($leadersPitching['era']->isNotEmpty())
                                @php $p = $leadersPitching['era']->first(); @endphp
                                <div class="size-12 rounded-full bg-slate-800 border border-accent/30 flex items-center justify-center font-black text-white mb-2 shadow-[0_0_15px_rgba(0,191,165,0.15)]">{{ $p->number ?? '00' }}</div>
                                <span class="text-xs font-bold text-slate-200 truncate w-full">{{ $p->name }} {{ $p->last_name }}</span>
                                <span class="text-lg font-black text-accent italic">{{ $p->currentStats?->era ?? '0.00' }}</span>
                            @else
                                <span class="text-xs text-slate-600">N/A</span>
                            @endif
                        </div>
                        {{-- SO --}}
                        <div class="flex flex-col items-center text-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">K</span>
                            @if($leadersPitching['so']->isNotEmpty())
                                @php $p = $leadersPitching['so']->first(); @endphp
                                <div class="size-12 rounded-full bg-slate-800 border border-white/5 flex items-center justify-center font-black text-white mb-2">{{ $p->number ?? '00' }}</div>
                                <span class="text-xs font-bold text-slate-200 truncate w-full">{{ $p->name }} {{ $p->last_name }}</span>
                                <span class="text-lg font-black text-white italic">{{ $p->currentStats?->p_so ?? 0 }}</span>
                            @else
                                <span class="text-xs text-slate-600">N/A</span>
                            @endif
                        </div>
                        {{-- WIN --}}
                        <div class="flex flex-col items-center text-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">GANADOS</span>
                            @if($leadersPitching['win']->isNotEmpty())
                                @php $p = $leadersPitching['win']->first(); @endphp
                                <div class="size-12 rounded-full bg-slate-800 border border-white/5 flex items-center justify-center font-black text-white mb-2">{{ $p->number ?? '00' }}</div>
                                <span class="text-xs font-bold text-slate-200 truncate w-full">{{ $p->name }} {{ $p->last_name }}</span>
                                <span class="text-lg font-black text-white italic">{{ $p->currentStats?->w ?? 0 }}</span>
                            @else
                                <span class="text-xs text-slate-600">N/A</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                {{-- Main Content: Roster & Stats Tables --}}
                <div class="lg:col-span-3 space-y-8">
                    {{-- Tablas de Estadísticas - Bateo --}}
                    <div class="glass rounded-2xl border border-white/5 overflow-hidden">
                        <div class="bg-black/40 px-6 py-4 border-b border-white/5 flex items-center justify-between">
                            <h2 class="text-sm font-black uppercase tracking-[0.2em] text-white italic flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-lg">sports_baseball</span>
                                <div>Estadísticas de Bateo</div>
                            </h2>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $hitters->count() }} Jugadores</span>
                        </div>
                        <div class="overflow-x-auto relative">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-white/5 border-b border-white/5 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                        <th class="py-3 px-4 text-center sticky left-0 bg-[#0a0c1f] z-10 w-16">#</th>
                                        <th class="py-3 px-4 min-w-[150px] sticky left-[64px] bg-[#0a0c1f] z-10 shadow-[4px_0_15px_-5px_rgba(0,0,0,0.5)]">Jugador</th>
                                        <th class="py-3 px-3 text-center">Pos</th>
                                        <th class="py-3 px-3 text-center">JJ</th>
                                        <th class="py-3 px-3 text-center">VB</th>
                                        <th class="py-3 px-3 text-center">C</th>
                                        <th class="py-3 px-3 text-center">H</th>
                                        <th class="py-3 px-3 text-center">2B</th>
                                        <th class="py-3 px-3 text-center">HR</th>
                                        <th class="py-3 px-3 text-center">CI</th>
                                        <th class="py-3 px-3 text-center">BB</th>
                                        <th class="py-3 px-3 text-center">K</th>
                                        <th class="py-3 px-3 text-center">BR</th>
                                        <th class="py-3 px-4 text-center text-primary">AVG</th>
                                        <th class="py-3 px-4 text-center text-white">OPS</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xs divide-y divide-white/5">
                                    @foreach($hitters->sortByDesc(fn($p) => (float) ($p->currentStats?->avg ?? 0)) as $player)
                                        <tr class="hover:bg-white/5 transition-colors group">
                                            <td class="py-3 px-4 text-center font-black text-slate-500 sticky left-0 group-hover:bg-[#15182e] transition-colors z-10">{{ $player->number ?? '-' }}</td>
                                            <td class="py-3 px-4 font-bold sticky left-[64px] group-hover:bg-[#15182e] transition-colors shadow-[4px_0_15px_-5px_rgba(0,0,0,0.5)] z-10 w-max whitespace-nowrap">
                                                <a href="{{ route('public.player.show', $player) }}" class="text-slate-200 hover:text-primary transition-colors flex items-center gap-2">
                                                    {{ $player->name }} {{ $player->last_name }}
                                                </a>
                                            </td>
                                            <td class="py-3 px-3 text-center text-slate-500 font-bold">{{ $player->position }}</td>
                                            <td class="py-3 px-3 text-center text-slate-300">{{ $player->currentStats?->g ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-300">{{ $player->currentStats?->ab ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-300">{{ $player->currentStats?->r ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-300 font-bold">{{ $player->currentStats?->h ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-400">{{ $player->currentStats?->doubles ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-200 font-bold">{{ $player->currentStats?->hr ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-200 font-bold">{{ $player->currentStats?->rbi ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-400">{{ $player->currentStats?->bb ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-400">{{ $player->currentStats?->so ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-400">{{ $player->currentStats?->sb ?? 0 }}</td>
                                            <td class="py-3 px-4 text-center font-black text-primary italic">{{ $player->currentStats?->avg ?? '.000' }}</td>
                                            <td class="py-3 px-4 text-center font-black text-white italic">{{ $player->currentStats?->ops ?? '.000' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tablas de Estadísticas - Pitcheo --}}
                    <div class="glass rounded-2xl border border-white/5 overflow-hidden">
                        <div class="bg-black/40 px-6 py-4 border-b border-white/5 flex items-center justify-between">
                            <h2 class="text-sm font-black uppercase tracking-[0.2em] text-white italic flex items-center gap-2">
                                <span class="material-symbols-outlined text-accent text-lg">sports_cricket</span>
                                <div>Estadísticas de Pitcheo</div>
                            </h2>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $pitchers->count() }} Lanzadores</span>
                        </div>
                        <div class="overflow-x-auto relative">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-white/5 border-b border-white/5 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                        <th class="py-3 px-4 text-center sticky left-0 bg-[#0a0c1f] z-10 w-16">#</th>
                                        <th class="py-3 px-4 min-w-[150px] sticky left-[64px] bg-[#0a0c1f] z-10 shadow-[4px_0_15px_-5px_rgba(0,0,0,0.5)]">Lanzador</th>
                                        <th class="py-3 px-3 text-center">G</th>
                                        <th class="py-3 px-3 text-center">P</th>
                                        <th class="py-3 px-3 text-center">SV</th>
                                        <th class="py-3 px-3 text-center">IP</th>
                                        <th class="py-3 px-3 text-center">H</th>
                                        <th class="py-3 px-3 text-center">CL</th>
                                        <th class="py-3 px-3 text-center">BB</th>
                                        <th class="py-3 px-3 text-center">K</th>
                                        <th class="py-3 px-4 text-center text-accent">ERA</th>
                                        <th class="py-3 px-4 text-center text-white">WHIP</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xs divide-y divide-white/5">
                                    @foreach($pitchers->sortBy(fn($p) => (float) ($p->currentStats?->era ?? 99.99)) as $player)
                                        <tr class="hover:bg-white/5 transition-colors group">
                                            <td class="py-3 px-4 text-center font-black text-slate-500 sticky left-0 group-hover:bg-[#15182e] transition-colors z-10">{{ $player->number ?? '-' }}</td>
                                            <td class="py-3 px-4 font-bold sticky left-[64px] group-hover:bg-[#15182e] transition-colors shadow-[4px_0_15px_-5px_rgba(0,0,0,0.5)] z-10 w-max whitespace-nowrap">
                                                <a href="{{ route('public.player.show', $player) }}" class="text-slate-200 hover:text-accent transition-colors flex items-center gap-2">
                                                    {{ $player->name }} {{ $player->last_name }}
                                                </a>
                                            </td>
                                            <td class="py-3 px-3 text-center text-slate-200 font-bold">{{ $player->currentStats?->w ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-400">{{ $player->currentStats?->l ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-300">{{ $player->currentStats?->sv ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-300">{{ $player->currentStats?->ip ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-400">{{ $player->currentStats?->p_h ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-400">{{ $player->currentStats?->p_er ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-400">{{ $player->currentStats?->p_bb ?? 0 }}</td>
                                            <td class="py-3 px-3 text-center text-slate-200 font-bold">{{ $player->currentStats?->p_so ?? 0 }}</td>
                                            <td class="py-3 px-4 text-center font-black text-accent italic">{{ $player->currentStats?->era ?? '0.00' }}</td>
                                            <td class="py-3 px-4 text-center font-black text-white italic">{{ $player->currentStats?->whip ?? '0.00' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Schedule Sidebar --}}
                <div class="space-y-6">
                    @if($nextGame)
                    {{-- Próximo Juego Destacado --}}
                    <div class="bg-gradient-to-br from-[#101223] to-[#0d0f1c] p-1 rounded-2xl border border-primary/30 shadow-[0_0_30px_rgba(255,110,64,0.1)] relative overflow-hidden group">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-primary to-transparent opacity-50"></div>
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-[10px] font-black uppercase tracking-widest text-primary italic blink-soft">Próximo Juego</h3>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-primary/20 text-primary border border-primary/30">{{ $nextGame->start_time->format('d/m/y H:i') }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between gap-4 mb-4">
                                <div class="flex flex-col items-center flex-1">
                                    <div class="size-10 rounded-full bg-slate-800/80 mb-2 border border-white/5 flex items-center justify-center font-bold text-xs">
                                        {{ substr($nextGame->homeTeam->name, 0, 3) }}
                                    </div>
                                    <span class="text-[10px] font-black uppercase text-center w-full truncate">{{ $nextGame->homeTeam->name }}</span>
                                    <span class="text-[9px] text-slate-500 uppercase tracking-widest mt-1">Local</span>
                                </div>
                                
                                <span class="text-xs font-black italic text-slate-600">VS</span>
                                
                                <div class="flex flex-col items-center flex-1">
                                    <div class="size-10 rounded-full bg-slate-800/80 mb-2 border border-white/5 flex items-center justify-center font-bold text-xs">
                                        {{ substr($nextGame->visitorTeam->name, 0, 3) }}
                                    </div>
                                    <span class="text-[10px] font-black uppercase text-center w-full truncate">{{ $nextGame->visitorTeam->name }}</span>
                                    <span class="text-[9px] text-slate-500 uppercase tracking-widest mt-1">Visitante</span>
                                </div>
                            </div>
                            
                            @if($nextGame->location)
                            <div class="text-[10px] text-center text-slate-400 border-t border-white/5 pt-3 mt-2 flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-xs">stadium</span>
                                {{ $nextGame->location }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <h2 class="text-sm font-black uppercase tracking-[0.2em] text-slate-500 italic flex items-center gap-2 px-2">
                        <span class="material-symbols-outlined text-white text-lg">calendar_month</span>
                        Últimos Resultados
                    </h2>

                    <div class="space-y-3">
                        @forelse($recentGames as $game)
                            <div class="glass relative overflow-hidden p-4 rounded-xl border border-white/5 hover:border-white/20 transition-colors cursor-default">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">{{ $game->start_time->format('d/m/Y') }}</span>
                                        <span class="text-xs font-bold text-slate-200">
                                            @if($game->home_team_id === $team->id)
                                                <span class="text-slate-500 italic mr-1">vs</span> {{ $game->visitorTeam->name }}
                                            @else
                                                <span class="text-slate-500 italic mr-1">@</span> {{ $game->homeTeam->name }}
                                            @endif
                                        </span>
                                    </div>

                                    <div class="flex flex-col items-end">
                                        @if($game->status === 'finished')
                                            @php
                                                $isWin = ($game->home_team_id === $team->id && $game->home_score > $game->visitor_score) || 
                                                         ($game->visitor_team_id === $team->id && $game->visitor_score > $game->home_score);
                                            @endphp
                                            <div class="px-2 py-1 rounded-md {{ $isWin ? 'bg-primary/10 border-primary/30 text-primary' : 'bg-slate-800/50 border-white/10 text-slate-400' }} border flex items-center gap-2">
                                                <span class="text-[10px] font-black uppercase tracking-widest">{{ $isWin ? 'W' : 'L' }}</span>
                                                <span class="text-sm font-black italic">
                                                    @if($game->home_team_id === $team->id)
                                                        {{ $game->home_score }}-{{ $game->visitor_score }}
                                                    @else
                                                        {{ $game->visitor_score }}-{{ $game->home_score }}
                                                    @endif
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">{{ $game->status }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="glass p-6 rounded-xl text-center border-dashed border-white/10">
                                <p class="text-slate-500 italic text-[10px] uppercase font-bold tracking-widest">Sin resultados recientes.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>