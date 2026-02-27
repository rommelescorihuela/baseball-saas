<x-layouts.public>
    <div class="relative min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        {{-- Background Decorations --}}
        <div class="absolute top-0 right-0 -z-10 size-[500px] bg-primary/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-0 left-0 -z-10 size-[300px] bg-accent/5 blur-[100px] rounded-full"></div>

        <div class="max-w-7xl mx-auto">
            {{-- Hero Section --}}
            <div class="mb-12 relative">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-3 mb-2">
                             <div class="size-12 rounded-2xl bg-primary/20 border border-primary/30 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-3xl font-bold">groups</span>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-primary italic">Academy Profile</h4>
                                <h1 class="text-4xl md:text-5xl font-display font-black tracking-tight text-white uppercase italic">
                                    {{ $team->name }}
                                </h1>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-slate-400">
                            <span class="material-symbols-outlined text-sm">location_on</span>
                            <span class="text-sm font-bold uppercase tracking-wider">{{ $team->city ?? 'Sede Principal' }}</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @foreach($team->categories->map(fn($cat) => $cat->league)->unique('id') as $league)
                            <div class="glass px-4 py-2 rounded-xl flex items-center gap-2">
                                <div class="size-2 rounded-full bg-primary animate-pulse"></div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-200">{{ $league->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Roster Column --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-between px-2">
                        <h2 class="text-sm font-black uppercase tracking-[0.2em] text-slate-500 italic flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-lg">person</span>
                            Active Roster
                        </h2>
                        <span class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">{{ $team->players->count() }} Players</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($team->players as $player)
                            <a href="{{ route('public.player.show', $player) }}" 
                               class="glass group relative overflow-hidden p-4 rounded-2xl border border-white/5 hover:border-primary/30 transition-all duration-500 hover:translate-y--1">
                                <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-30 transition-opacity">
                                    <span class="material-symbols-outlined text-4xl transform rotate-12">sports_baseball</span>
                                </div>
                                
                                <div class="flex items-center gap-4 relative z-10">
                                    <div class="size-12 rounded-xl bg-slate-800/50 border border-white/10 flex items-center justify-center text-xl font-black text-primary italic">
                                        {{ $player->number ?? '00' }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-100 group-hover:text-primary transition-colors">{{ $player->name }} {{ $player->last_name }}</h3>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{ $player->position ?? 'PL' }}</p>
                                    </div>
                                    <div class="ml-auto flex flex-col items-end">
                                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-500">AVG</div>
                                        <div class="text-xs font-black text-primary italic">{{ $player->currentStats?->avg ?? '.000' }}</div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="col-span-full glass p-8 rounded-2xl text-center border-dashed border-white/10">
                                <p class="text-slate-500 italic text-sm">No players assigned to this roster yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Schedule Column --}}
                <div class="space-y-6">
                    <h2 class="text-sm font-black uppercase tracking-[0.2em] text-slate-500 italic flex items-center gap-2 px-2">
                        <span class="material-symbols-outlined text-accent text-lg">calendar_today</span>
                        Recent Schedule
                    </h2>

                    <div class="space-y-3">
                        @forelse($games as $game)
                            <div class="glass relative overflow-hidden p-4 rounded-2xl border border-white/5">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ $game->start_time->format('D, M d') }}</span>
                                        <span class="text-xs font-bold text-slate-200">
                                            @if($game->home_team_id === $team->id)
                                                <span class="text-primary italic font-black">VS</span> {{ $game->visitorTeam->name }}
                                            @else
                                                <span class="text-accent italic font-black">@</span> {{ $game->homeTeam->name }}
                                            @endif
                                        </span>
                                    </div>

                                    <div class="flex flex-col items-end">
                                        @if($game->status === 'finished')
                                            <div class="px-3 py-1 rounded-lg bg-primary/10 border border-primary/20">
                                                <span class="text-sm font-black text-primary italic">
                                                    @if($game->home_team_id === $team->id)
                                                        {{ $game->home_score }}-{{ $game->visitor_score }}
                                                    @else
                                                        {{ $game->visitor_score }}-{{ $game->home_score }}
                                                    @endif
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded bg-slate-800 text-slate-400">{{ $game->status }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="glass p-8 rounded-2xl text-center border-dashed border-white/10">
                                <p class="text-slate-500 italic text-sm">No recent or upcoming games.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Action Card --}}
                    <div class="bg-gradient-to-br from-primary/20 to-accent/20 p-6 rounded-2xl border border-white/10 relative overflow-hidden group">
                         <div class="absolute -right-4 -bottom-4 size-24 bg-primary/20 blur-2xl rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                         <h3 class="text-sm font-black uppercase tracking-widest text-white italic mb-2">Diamond Analytics</h3>
                         <p class="text-xs text-slate-300 mb-4 leading-relaxed">Access full statistics and performance historical data for this academy.</p>
                         <button class="w-full py-2 bg-primary px-4 rounded-lg text-background-dark text-[10px] font-black uppercase tracking-widest hover:bg-white transition-colors">
                             View Full Stats
                         </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>