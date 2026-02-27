<x-layouts.public title="{{ $seoTitle }}">
    <!-- Back Button & Header -->
    <div
        class="sticky top-0 z-50 flex items-center bg-background-dark/80 backdrop-blur-md p-4 border-b border-white/5 justify-between">
        <a href="{{ route('public.team.show', $player->team) }}"
            class="text-primary flex size-10 shrink-0 items-center justify-center cursor-pointer transition hover:bg-white/5 rounded-full">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="flex flex-col items-center flex-1">
            <h2 class="text-white text-sm font-bold leading-tight tracking-tight uppercase">DiamondOS Analytics</h2>
            <p class="text-[10px] text-primary font-medium tracking-[0.2em] uppercase">Player Performance</p>
        </div>
        <div class="flex w-10 items-center justify-end">
            <button
                class="flex items-center justify-center rounded-full size-10 bg-primary/10 text-primary border border-primary/20">
                <span class="material-symbols-outlined text-[20px]">share</span>
            </button>
        </div>
    </div>

    <!-- Hero Player Info (Byte-Perfect from Stitch) -->
    <div class="relative overflow-hidden px-4 py-6">
        <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent opacity-30"></div>
        <div class="relative flex w-full flex-col gap-4">
            <div class="flex gap-5 items-center">
                <div class="relative">
                    <div
                        class="flex items-center justify-center rounded-full h-24 w-24 border-2 border-primary shadow-[0_0_15px_rgba(0,229,255,0.15)] bg-slate-800">
                        <span
                            class="text-3xl font-black text-white italic uppercase">{{ substr($player->name, 0, 1) }}{{ substr($player->last_name, 0, 1) }}</span>
                    </div>
                    <div
                        class="absolute -bottom-1 -right-1 bg-primary text-background-dark text-[10px] font-bold px-2 py-0.5 rounded-full">
                        #{{ $player->number ?? '00' }}
                    </div>
                </div>
                <div class="flex flex-col justify-center">
                    <p class="text-white text-2xl font-bold leading-tight tracking-tight">{{ $player->name }}
                        {{ $player->last_name }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span
                            class="text-primary text-sm font-semibold uppercase">{{ $player->team->name ?? 'Free Agent' }}</span>
                        <span class="h-1 w-1 rounded-full bg-slate-500"></span>
                        <p class="text-slate-400 text-sm font-medium uppercase">{{ $player->position ?? 'UTIL' }}</p>
                    </div>
                    <div class="flex gap-2 mt-2">
                        <span
                            class="bg-primary/20 text-primary text-[10px] px-2 py-0.5 rounded uppercase font-bold tracking-wider">Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Season Totals (Glass Cards) -->
    <div class="px-4 pb-2">
        <h3 class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.15em] mb-3">Season Totals</h3>
        <div class="flex gap-3 overflow-x-auto pb-4 no-scrollbar">
            <div
                class="flex min-w-[100px] flex-col gap-1 rounded-xl p-4 glass bg-slate-900/60 shadow-[0_0_15px_rgba(0,229,255,0.05)] border-white/5 shrink-0">
                <p class="text-slate-400 text-[10px] font-bold uppercase">AVG</p>
                <p class="text-primary tracking-tighter text-2xl font-bold">{{ $player->currentStats?->avg ?? '.000' }}
                </p>
            </div>
            <div
                class="flex min-w-[100px] flex-col gap-1 rounded-xl p-4 glass bg-slate-900/60 shadow-[0_0_15px_rgba(0,229,255,0.05)] border-white/5 shrink-0">
                <p class="text-slate-400 text-[10px] font-bold uppercase">HR</p>
                <p class="text-white tracking-tighter text-2xl font-bold">{{ $player->currentStats?->hr ?? '0' }}</p>
            </div>
            <div
                class="flex min-w-[100px] flex-col gap-1 rounded-xl p-4 glass bg-slate-900/60 shadow-[0_0_15px_rgba(0,229,255,0.05)] border-white/5 shrink-0">
                <p class="text-slate-400 text-[10px] font-bold uppercase">RBI</p>
                <p class="text-white tracking-tighter text-2xl font-bold">{{ $player->currentStats?->rbi ?? '0' }}</p>
            </div>
            <div
                class="flex min-w-[100px] flex-col gap-1 rounded-xl p-4 glass bg-slate-900/60 shadow-[0_0_15px_rgba(0,229,255,0.05)] border-white/5 shrink-0">
                <p class="text-slate-400 text-[10px] font-bold uppercase">HITS</p>
                <p class="text-primary tracking-tighter text-2xl font-bold">{{ $player->currentStats?->h ?? '0' }}</p>
            </div>
            <div
                class="flex min-w-[100px] flex-col gap-1 rounded-xl p-4 glass bg-slate-900/60 shadow-[0_0_15px_rgba(0,229,255,0.05)] border-white/5 shrink-0">
                <p class="text-slate-400 text-[10px] font-bold uppercase">SB</p>
                <p class="text-white tracking-tighter text-2xl font-bold">{{ $player->currentStats?->sb ?? '0' }}</p>
            </div>
        </div>
    </div>

    <div class="px-4 mt-4 flex flex-col gap-8 mb-20">
        <!-- Trend Decorator Stitch Graphic -->
        <div>
            <h3 class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.15em] mb-3">Season Performance Trend
            </h3>
            <div class="relative h-40 rounded-xl bg-brand-navy/30 border border-primary/10 overflow-hidden pt-4">
                <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 40">
                    <defs>
                        <linearGradient id="gradient" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#00e5ff" stop-opacity="0.3"></stop>
                            <stop offset="100%" stop-color="#00e5ff" stop-opacity="0"></stop>
                        </linearGradient>
                    </defs>
                    <path d="M0,35 Q10,25 20,30 T40,15 T60,20 T80,5 T100,10 L100,40 L0,40 Z" fill="url(#gradient)">
                    </path>
                    <path d="M0,35 Q10,25 20,30 T40,15 T60,20 T80,5 T100,10" fill="none" stroke="#00e5ff"
                        stroke-width="1"></path>
                </svg>
                <div
                    class="absolute bottom-2 left-0 w-full flex justify-between px-4 text-[8px] text-slate-500 font-bold">
                    <span>INICIO</span><span>MEDIA</span><span>ACTUAL</span>
                </div>
            </div>
        </div>

        <!-- Recent Games Analytics Table -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.15em]">Recent Games</h3>
                <span class="text-primary text-[10px] font-bold uppercase">Last
                    {{ min($player->stats->count(), 10) }}</span>
            </div>

            <div class="overflow-x-auto rounded-xl border border-primary/10 bg-brand-navy/20">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-brand-navy/40 text-[9px] uppercase font-bold text-slate-400 border-b border-white/5">
                            <th class="p-3">Date</th>
                            <th class="p-3">Opp</th>
                            <th class="p-3 text-center">AB</th>
                            <th class="p-3 text-center">R</th>
                            <th class="p-3 text-center">H</th>
                            <th class="p-3 text-center text-primary">RBI</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs font-medium">
                        @forelse($player->stats->sortByDesc(fn($s) => $s->game->start_time)->take(10) as $stat)
                            <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                <td class="p-3 text-slate-400">{{ $stat->game->start_time->format('d/m') }}</td>
                                <td class="p-3 text-slate-100">
                                    @if($stat->game->home_team_id === $player->team_id)
                                        <span class="text-primary italic">VS</span> <span
                                            class="uppercase">{{ substr($stat->game->visitorTeam->name, 0, 3) }}</span>
                                    @else
                                        <span class="text-accent italic">@</span> <span
                                            class="uppercase">{{ substr($stat->game->homeTeam->name, 0, 3) }}</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center text-slate-300">{{ $stat->ab }}</td>
                                <td class="p-3 text-center text-slate-300">{{ $stat->r }}</td>
                                <td
                                    class="p-3 text-center {{ $stat->h > 0 ? 'text-[#0bda54] font-bold' : 'text-slate-500' }}">
                                    {{ $stat->h }}</td>
                                <td
                                    class="p-3 text-center {{ $stat->rbi > 0 ? 'text-primary font-bold' : 'text-slate-500' }}">
                                    {{ $stat->rbi }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    class="p-6 text-center text-slate-500 text-[10px] uppercase tracking-widest italic font-bold">
                                    No game data logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts.public>