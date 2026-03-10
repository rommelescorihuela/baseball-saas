<x-layouts.public title="{{ $seoTitle }}">
    <!-- Back Button & Header -->
    <div
        class="sticky top-0 z-50 flex items-center bg-background-dark/80 backdrop-blur-md p-4 border-b border-white/5 justify-between">
        <a href="{{ $player->team ? route('public.team.show', $player->team) : 'javascript:history.back()' }}"
            class="text-primary flex size-10 shrink-0 items-center justify-center cursor-pointer transition hover:bg-white/5 rounded-full">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="flex flex-col items-center flex-1">
            <h2 class="text-white text-sm font-bold leading-tight tracking-tight uppercase">DiamondOS Analytics</h2>
            <p class="text-[10px] text-primary font-medium tracking-[0.2em] uppercase">Player Showcase Card</p>
        </div>
        <div class="flex w-10 items-center justify-end">
            <button
                class="flex items-center justify-center rounded-full size-10 bg-primary/10 text-primary border border-primary/20 hover:bg-primary/20 transition-colors">
                <span class="material-symbols-outlined text-[20px]">share</span>
            </button>
        </div>
    </div>

    <!-- Hero Player Info (Premium Layout) -->
    <div class="relative overflow-hidden px-4 md:px-8 py-8">
        <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-brand-navy/50 opacity-50"></div>
        <div class="relative flex flex-col md:flex-row md:items-center gap-6">
            <div class="flex items-center gap-6">
                <!-- Avatar / Number -->
                <div class="relative group">
                    <div
                        class="flex items-center justify-center rounded-full h-32 w-32 md:h-40 md:w-40 border-[3px] border-primary shadow-[0_0_30px_rgba(0,229,255,0.25)] bg-slate-900 transition-transform duration-500 group-hover:scale-105">
                        <span class="text-5xl md:text-6xl font-black text-white italic tracking-tighter shadow-sm blur-[0.3px]">
                            {{ substr($player->name, 0, 1) }}{{ substr($player->last_name, 0, 1) }}
                        </span>
                    </div>
                    <div
                        class="absolute -bottom-2 -right-2 bg-gradient-to-br from-primary to-blue-400 text-background-dark text-lg md:text-xl font-black px-3 py-1 rounded-full shadow-[0_4px_10px_rgba(0,0,0,0.5)] border-2 border-slate-900">
                        #{{ $player->number ?? '00' }}
                    </div>
                </div>

                <!-- Info block -->
                <div class="flex flex-col justify-center">
                    <div class="inline-flex mb-1">
                        <span class="bg-primary/20 border border-primary/30 text-primary text-[10px] px-2 py-0.5 rounded font-bold uppercase tracking-widest shadow-[0_0_10px_rgba(0,229,255,0.1)]">
                            Scouting Report
                        </span>
                    </div>
                    <h1 class="text-white text-3xl md:text-4xl font-black italic tracking-tight drop-shadow-md">
                        {{ $player->name }} <br/><span class="text-primary/90">{{ $player->last_name }}</span>
                    </h1>
                    
                    <div class="flex items-center gap-2 mt-3 bg-brand-navy/40 w-fit px-3 py-1.5 rounded-lg border border-white/5">
                        <span class="material-symbols-outlined text-primary text-sm">sports_baseball</span>
                        <span class="text-white text-xs font-bold uppercase tracking-wider">{{ $player->position ?? 'UTIL' }}</span>
                        <span class="h-4 w-[1px] bg-white/20 mx-1"></span>
                        <span class="text-slate-300 text-xs font-semibold uppercase">{{ $player->team->name ?? 'Free Agent' }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Quick Season Mini-Stats -->
            <div class="mt-4 md:mt-0 md:ml-auto flex gap-3 md:gap-4 overflow-x-auto pb-2 no-scrollbar">
                @if(!$isPitcher)
                <div class="flex flex-col items-center justify-center rounded-xl p-3 md:p-4 bg-brand-navy/60 border border-white/10 min-w-[80px]">
                    <p class="text-slate-400 text-[10px] font-bold uppercase">AVG</p>
                    <p class="text-primary text-xl font-black">{{ $player->currentStats?->avg ?? '.000' }}</p>
                </div>
                <div class="flex flex-col items-center justify-center rounded-xl p-3 md:p-4 bg-brand-navy/60 border border-white/10 min-w-[80px]">
                    <p class="text-slate-400 text-[10px] font-bold uppercase">HR</p>
                    <p class="text-white text-xl font-black">{{ $player->currentStats?->hr ?? '0' }}</p>
                </div>
                <div class="flex flex-col items-center justify-center rounded-xl p-3 md:p-4 bg-brand-navy/60 border border-white/10 min-w-[80px]">
                    <p class="text-slate-400 text-[10px] font-bold uppercase">OPS</p>
                    <p class="text-white text-xl font-black">{{ $player->currentStats?->ops ?? '.000' }}</p>
                </div>
                @else
                <div class="flex flex-col items-center justify-center rounded-xl p-3 md:p-4 bg-brand-navy/60 border border-white/10 min-w-[80px]">
                    <p class="text-slate-400 text-[10px] font-bold uppercase">ERA</p>
                    <p class="text-primary text-xl font-black">{{ $player->currentStats?->era ?? '0.00' }}</p>
                </div>
                <div class="flex flex-col items-center justify-center rounded-xl p-3 md:p-4 bg-brand-navy/60 border border-white/10 min-w-[80px]">
                    <p class="text-slate-400 text-[10px] font-bold uppercase">K</p>
                    <p class="text-white text-xl font-black">{{ $player->currentStats?->p_so ?? '0' }}</p>
                </div>
                <div class="flex flex-col items-center justify-center rounded-xl p-3 md:p-4 bg-brand-navy/60 border border-white/10 min-w-[80px]">
                    <p class="text-slate-400 text-[10px] font-bold uppercase">W</p>
                    <p class="text-white text-xl font-black">{{ $player->currentStats?->w ?? '0' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT GRID -->
    <div class="px-4 md:px-8 py-6 flex flex-col lg:flex-row gap-8 mb-20">
        
        <!-- LEFT COLUMN (Radar & Scouting) -->
        <div class="w-full lg:w-1/3 flex flex-col gap-6">
            <div class="bg-brand-navy/30 border border-primary/20 rounded-2xl p-5 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full blur-2xl"></div>
                <h3 class="text-slate-300 text-[11px] font-bold uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[14px]">radar</span>
                    Evaluation Radar
                </h3>
                
                <div class="relative w-full aspect-square max-w-[300px] mx-auto">
                    <canvas id="scoutingRadar"></canvas>
                </div>
                
                <!-- Metrics List -->
                <div class="mt-6 flex flex-col gap-3">
                    @foreach($scouting as $tool => $score)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ $tool }}</span>
                        <div class="flex items-center gap-3 flex-1 ml-4 shadow-sm">
                            <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden border border-white/5">
                                <div class="h-full bg-gradient-to-r from-primary/50 to-primary rounded-full transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(0,229,255,0.5)]" style="width: {{ $score }}%"></div>
                            </div>
                            <span class="text-white font-black text-xs w-6 text-right">{{ number_format($score, 0) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN (Game Log & Career Stats) -->
        <div class="w-full lg:w-2/3 flex flex-col gap-8">
            
            <!-- Game Log -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-slate-300 text-[12px] font-bold uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[16px]">calendar_month</span>
                        Game Log (Last {{ $gameLog->count() }})
                    </h3>
                </div>

                <div class="overflow-x-auto rounded-xl border border-white/10 bg-brand-navy/20 shadow-lg custom-scrollbar">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-brand-navy/60 text-[10px] uppercase font-black tracking-wider text-primary border-b border-primary/20">
                                <th class="p-3">Date</th>
                                <th class="p-3">Opponent</th>
                                @if(!$isPitcher)
                                <th class="p-3 text-center">AB</th>
                                <th class="p-3 text-center">R</th>
                                <th class="p-3 text-center">H</th>
                                <th class="p-3 text-center">2B</th>
                                <th class="p-3 text-center border-x border-white/5 bg-white/5">HR</th>
                                <th class="p-3 text-center">RBI</th>
                                <th class="p-3 text-center">BB</th>
                                <th class="p-3 text-center">K</th>
                                @else
                                <th class="p-3 text-center text-white">DEC</th>
                                <th class="p-3 text-center border-x border-white/5 bg-white/5">IP</th>
                                <th class="p-3 text-center">H</th>
                                <th class="p-3 text-center">R</th>
                                <th class="p-3 text-center">ER</th>
                                <th class="p-3 text-center">BB</th>
                                <th class="p-3 text-center">SO</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="text-xs font-medium text-slate-300">
                            @forelse($gameLog as $stat)
                                <tr class="border-b border-white/5 hover:bg-white/5 transition-colors group">
                                    <td class="p-3">{{ $stat->game->start_time->format('M d') }}</td>
                                    <td class="p-3 font-bold">
                                        @if($stat->game->home_team_id === $player->team_id)
                                            <span class="text-slate-500 font-normal mr-1">vs</span> <span class="text-white">{{ Str::limit($stat->game->visitorTeam->name, 12) }}</span>
                                        @else
                                            <span class="text-primary font-normal mr-1">@</span> <span class="text-white">{{ Str::limit($stat->game->homeTeam->name, 12) }}</span>
                                        @endif
                                    </td>
                                    @if(!$isPitcher)
                                    <td class="p-3 text-center">{{ $stat->ab }}</td>
                                    <td class="p-3 text-center">{{ $stat->r }}</td>
                                    <td class="p-3 text-center {{ $stat->h > 0 ? 'text-primary font-bold' : '' }}">{{ $stat->h }}</td>
                                    <td class="p-3 text-center">{{ $stat->doubles ?? 0 }}</td>
                                    <td class="p-3 text-center border-x border-white/5 bg-brand-navy/30 {{ ($stat->hr ?? 0) > 0 ? 'text-green-400 font-bold' : '' }}">{{ $stat->hr ?? 0 }}</td>
                                    <td class="p-3 text-center {{ $stat->rbi > 0 ? 'text-white font-bold' : '' }}">{{ $stat->rbi }}</td>
                                    <td class="p-3 text-center">{{ $stat->bb ?? 0 }}</td>
                                    <td class="p-3 text-center opacity-60">{{ $stat->so ?? 0 }}</td>
                                    @else
                                    <td class="p-3 text-center font-bold">
                                        @if($stat->w > 0) <span class="text-green-400">W</span>
                                        @elseif($stat->l > 0) <span class="text-red-400">L</span>
                                        @elseif($stat->sv > 0) <span class="text-primary">SV</span>
                                        @else <span class="text-slate-500">-</span> @endif
                                    </td>
                                    <td class="p-3 text-center border-x border-white/5 bg-brand-navy/30 text-white font-bold">{{ $stat->ip }}</td>
                                    <td class="p-3 text-center">{{ $stat->p_h }}</td>
                                    <td class="p-3 text-center">{{ $stat->p_r }}</td>
                                    <td class="p-3 text-center">{{ $stat->p_er }}</td>
                                    <td class="p-3 text-center">{{ $stat->p_bb }}</td>
                                    <td class="p-3 text-center {{ $stat->p_so >= 3 ? 'text-primary font-bold' : '' }}">{{ $stat->p_so }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="p-8 text-center text-slate-500 text-[11px] uppercase tracking-[0.2em] font-bold">
                                        No recent game logs found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Career Stats -->
            <div>
                <div class="flex items-center justify-between mb-4 mt-4">
                    <h3 class="text-slate-300 text-[12px] font-bold uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[16px]">history</span>
                        Career Stats (Season by Season)
                    </h3>
                </div>

                <div class="overflow-x-auto rounded-xl border border-white/10 bg-brand-navy/20 shadow-lg custom-scrollbar">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-primary/10 text-[10px] uppercase font-black tracking-wider text-white border-b border-primary/30">
                                <th class="p-3">Season</th>
                                <th class="p-3 text-center">G</th>
                                @if(!$isPitcher)
                                <th class="p-3 text-center text-slate-400">AB</th>
                                <th class="p-3 text-center text-slate-400">R</th>
                                <th class="p-3 text-center text-slate-400">H</th>
                                <th class="p-3 text-center text-slate-400">HR</th>
                                <th class="p-3 text-center text-slate-400">RBI</th>
                                <th class="p-3 text-center text-slate-400">SB</th>
                                <th class="p-3 text-center text-primary border-l border-white/10">AVG</th>
                                <th class="p-3 text-center text-primary">OBP</th>
                                <th class="p-3 text-center text-primary">SLG</th>
                                <th class="p-3 text-center text-primary bg-white/5 border-x border-white/10">OPS</th>
                                @else
                                <th class="p-3 text-center text-slate-400">W-L</th>
                                <th class="p-3 text-center text-slate-400">IP</th>
                                <th class="p-3 text-center text-slate-400">H</th>
                                <th class="p-3 text-center text-slate-400">ER</th>
                                <th class="p-3 text-center text-slate-400">BB</th>
                                <th class="p-3 text-center text-slate-400">SO</th>
                                <th class="p-3 text-center text-primary bg-white/5 border-x border-white/10">ERA</th>
                                <th class="p-3 text-center text-primary">WHIP</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="text-xs font-medium text-slate-200">
                            @forelse($careerStats as $seasonId => $cs)
                                <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                    <td class="p-3 font-bold text-white">{{ $cs['season_name'] }}</td>
                                    <td class="p-3 text-center">{{ $cs['g'] }}</td>
                                    @if(!$isPitcher)
                                    <td class="p-3 text-center text-slate-400">{{ $cs['ab'] }}</td>
                                    <td class="p-3 text-center text-slate-400">{{ $cs['r'] }}</td>
                                    <td class="p-3 text-center text-slate-400">{{ $cs['h'] }}</td>
                                    <td class="p-3 text-center text-slate-400">{{ $cs['hr'] }}</td>
                                    <td class="p-3 text-center text-slate-400">{{ $cs['rbi'] }}</td>
                                    <td class="p-3 text-center text-slate-400">{{ $cs['sb'] }}</td>
                                    <td class="p-3 text-center font-bold border-l border-white/10">{{ $cs['avg'] }}</td>
                                    <td class="p-3 text-center font-bold">{{ $cs['obp'] }}</td>
                                    <td class="p-3 text-center font-bold">{{ $cs['slg'] }}</td>
                                    <td class="p-3 text-center font-black bg-white/5 border-x border-white/10 shadow-[inset_0_0_10px_rgba(0,0,0,0.2)]">{{ $cs['ops'] }}</td>
                                    @else
                                    <td class="p-3 text-center text-slate-400">{{ $cs['w'] }}-{{ $cs['l'] }}</td>
                                    <td class="p-3 text-center text-slate-400">{{ $cs['ip'] }}</td>
                                    <td class="p-3 text-center text-slate-400">{{ $cs['p_h'] }}</td>
                                    <td class="p-3 text-center text-slate-400">{{ $cs['er'] }}</td>
                                    <td class="p-3 text-center text-slate-400">{{ $cs['p_bb'] }}</td>
                                    <td class="p-3 text-center text-slate-400">{{ $cs['p_so'] }}</td>
                                    <td class="p-3 text-center font-black bg-white/5 border-x border-white/10 shadow-[inset_0_0_10px_rgba(0,0,0,0.2)]">{{ $cs['era'] }}</td>
                                    <td class="p-3 text-center font-bold">{{ $cs['whip'] }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ !$isPitcher ? 12 : 10 }}" class="p-8 text-center text-slate-500 text-[11px] uppercase tracking-[0.2em] font-bold">
                                        No historical data available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>

    <!-- Chart.js Injection & Initialization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('scoutingRadar');
            if (ctx) {
                // Parse the PHP array into JS arrays (Keys for labels, Values for Data)
                const scoutData = @json($scouting);
                const labels = Object.keys(scoutData);
                const data = Object.values(scoutData);

                new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Scouting Score (Mecánica y Analítica)',
                            data: data,
                            backgroundColor: 'rgba(0, 229, 255, 0.2)', // Primary con opacidad (Relleno Neon)
                            borderColor: '#00e5ff',                     // Primary border
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#00e5ff',
                            pointHoverBackgroundColor: '#00e5ff',
                            pointHoverBorderColor: '#fff',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)', // bg-slate-900
                                titleColor: '#00e5ff', // primary
                                bodyColor: '#fff',
                                borderColor: 'rgba(255,255,255,0.1)',
                                borderWidth: 1,
                                displayColors: false,
                                padding: 12,
                                bodyFont: { size: 14, weight: 'bold' }
                            }
                        },
                        scales: {
                            r: {
                                angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                                grid: { color: 'rgba(255, 255, 255, 0.1)', circular: true },
                                pointLabels: {
                                    color: '#94a3b8', // text-slate-400
                                    font: { size: 10, family: 'Inter', weight: 'bold' }
                                },
                                ticks: {
                                    display: false,
                                    min: 0,
                                    max: 100,
                                    stepSize: 20
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,229,255,0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(0,229,255,0.5); }
    </style>
</x-layouts.public>