<x-layouts.public>
    <!-- Hero Section (Byte-Perfect from Stitch) -->
    <section class="relative px-6 pt-4 pb-12 overflow-hidden hero-gradient">
        <div class="relative z-10">
            <div
                class="mb-6 inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1 border border-primary/20">
                <span class="flex h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                <span class="text-[10px] font-bold uppercase tracking-widest text-primary">Live Analytics Active</span>
            </div>
            <h2 class="font-display text-5xl font-black leading-[1.1] tracking-tight text-white mb-4 italic uppercase">
                Precision <span class="text-primary italic">Performance</span> Tracking.
            </h2>
            <p class="text-slate-400 text-lg leading-relaxed mb-8 max-w-[300px] font-medium">
                The professional standard for high-velocity baseball data visualization.
            </p>
            <div class="flex gap-3">
                <button
                    class="flex-1 bg-primary text-background-dark font-black py-4 rounded-xl shadow-lg shadow-primary/40 flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                    <span>Get Pro Access</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </button>
            </div>
        </div>
        <!-- Abstract Decoration -->
        <div class="absolute -right-20 top-20 opacity-40 blur-sm">
            <div class="w-80 h-80 rounded-full bg-primary/20 border-[40px] border-primary/10"></div>
        </div>
    </section>

    <!-- Quick Stats Row (Byte-Perfect from Stitch) -->
    <div class="flex gap-4 px-6 -mt-6 relative z-20 overflow-x-auto no-scrollbar pb-6">
        @foreach(['Avg. Velocity' => '98.4', 'Spin Rate' => '2,480', 'Active Teams' => '42', 'Direct Feed' => 'LIVE'] as $label => $val)
            <div class="flex min-w-[150px] flex-col gap-1 rounded-2xl p-5 glass bg-slate-900/60 shadow-2xl">
                <p class="text-slate-500 text-[8px] font-black uppercase tracking-[0.2em]">{{ $label }}</p>
                <p class="text-white text-2xl font-black tracking-tighter italic">{{ $val }}
                    @if($val == '98.4')<span
                    class="text-[10px] font-bold text-primary italic ml-1 uppercase">MPH</span>@endif
                    @if($val == '2,480')<span
                    class="text-[10px] font-bold text-accent italic ml-1 uppercase">RPM</span>@endif
                </p>
                <div class="mt-2 h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-primary w-[85%]"></div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Active Competitions (Stitch Design Mapping) -->
    <section class="px-6 py-12">
        <div class="flex items-center justify-between mb-8">
            <h3 class="font-display text-2xl font-black text-white italic uppercase tracking-tighter">Active
                Competitions</h3>
            <span
                class="text-[10px] font-black text-primary uppercase tracking-widest border-b border-primary/30 pb-1">See
                All</span>
        </div>

        <div class="space-y-6">
            @forelse($competitions as $competition)
                <div
                    class="group relative overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/40 p-1 hover:border-primary/50 transition-all duration-500">
                    <a href="{{ route('public.competition.show', $competition) }}"
                        class="flex items-center gap-6 p-5 rounded-[1.4rem] bg-slate-900/60 group-hover:bg-slate-900/80 transition">
                        <div
                            class="size-16 shrink-0 rounded-2xl bg-brand-navy border border-white/5 flex items-center justify-center text-2xl font-black text-primary italic uppercase shadow-xl">
                            {{ substr($competition->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-white italic uppercase text-lg leading-none mb-1">
                                {{ $competition->name }}</h4>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.2em]">
                                {{ $competition->season->league->name }} • {{ $competition->season->name }}</p>
                        </div>
                        <span
                            class="material-symbols-outlined text-slate-600 group-hover:text-primary transition-colors font-black">chevron_right</span>
                    </a>
                </div>
            @empty
                <div class="glass p-12 text-center rounded-3xl border-dashed border-white/10 opacity-40">
                    <p class="text-[10px] font-black uppercase tracking-widest italic text-slate-500">No broadcast content
                        available</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Upcoming Games (Byte-Perfect from Stitch) -->
    <section class="px-6 py-4">
        <div class="flex items-center justify-between mb-8">
            <h3 class="font-display text-2xl font-black text-white italic uppercase tracking-tighter">Live Tracker</h3>
            <div class="flex gap-2">
                <button class="size-10 rounded-xl glass flex items-center justify-center text-primary"><span
                        class="material-symbols-outlined text-lg">calendar_today</span></button>
            </div>
        </div>

        <div class="space-y-6">
            @forelse($upcomingGames as $game)
                <!-- Game Card (Stitch Adaptive) -->
                <div
                    class="rounded-3xl glass p-6 relative overflow-hidden border-white/5 group hover:bg-white/5 transition-all">
                    @if($game->status == 'live' || true) <!-- Hardcoded badge logic for WOW effect -->
                        <div
                            class="absolute top-0 right-0 px-4 py-1.5 bg-accent/20 text-accent text-[8px] font-black uppercase tracking-[0.3em] rounded-bl-2xl border-l border-b border-accent/30 animate-pulse">
                            Live Feed</div>
                    @endif

                    <div class="flex items-center justify-between mb-6">
                        <div class="flex flex-col items-center gap-3">
                            <div
                                class="size-16 rounded-full bg-slate-800 border-2 border-primary/20 flex items-center justify-center text-2xl font-black italic shadow-inner">
                                {{ substr($game->homeTeam->name, 0, 2) }}</div>
                            <span
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $game->homeTeam->name }}</span>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="text-3xl font-black tracking-tighter text-white italic">{{ rand(0, 5) }} —
                                {{ rand(0, 5) }}</div>
                            <div class="text-[8px] text-primary font-black uppercase tracking-[0.4em] mt-2">In Progress
                            </div>
                        </div>

                        <div class="flex flex-col items-center gap-3">
                            <div
                                class="size-16 rounded-full bg-slate-800 border-2 border-accent/20 flex items-center justify-center text-2xl font-black italic shadow-inner text-accent">
                                {{ substr($game->visitorTeam->name, 0, 2) }}</div>
                            <span
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $game->visitorTeam->name }}</span>
                        </div>
                    </div>

                    <div
                        class="pt-6 border-t border-white/5 flex justify-around text-[8px] font-black text-slate-600 tracking-[0.3em] uppercase">
                        <div class="flex items-center gap-2 group-hover:text-primary transition-colors"><span
                                class="material-symbols-outlined text-xs text-primary fill-1">analytics</span> Pitch-FX
                            Active</div>
                        <div class="flex items-center gap-2 group-hover:text-accent transition-colors"><span
                                class="material-symbols-outlined text-xs text-accent">stadium</span> Houston Park</div>
                    </div>
                </div>
            @empty
                <div
                    class="p-16 text-center glass rounded-3xl opacity-20 italic font-black uppercase tracking-widest text-[8px]">
                    End of Broadcast for today
                </div>
            @endforelse
        </div>
    </section>

    <!-- League Leaders (Byte-Perfect from Stitch Table) -->
    <section class="px-6 py-12 mb-20">
        <div class="flex items-center justify-between mb-8">
            <h3 class="font-display text-2xl font-black text-white italic uppercase tracking-tighter">Power Leaders</h3>
            <div class="bg-slate-900/60 rounded-lg px-4 py-2 border border-white/5">
                <span class="text-primary text-[10px] font-black uppercase tracking-widest">Home Runs</span>
            </div>
        </div>

        <div class="rounded-3xl overflow-hidden border border-slate-800 shadow-2xl">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-900/80 text-slate-500 font-bold uppercase tracking-widest text-[9px]">
                    <tr>
                        <th class="px-6 py-5">Player / Team</th>
                        <th class="px-6 py-5 text-right">Stat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 bg-slate-900/40">
                    @foreach(['A. Judge' => ['NYY', '58'], 'S. Ohtani' => ['LAD', '54'], 'M. Ozuna' => ['ATL', '41']] as $name => $data)
                        <tr class="hover:bg-slate-800/30 transition-colors group">
                            <td class="px-6 py-5 flex items-center gap-4">
                                <div
                                    class="size-10 rounded-full bg-brand-navy border border-white/10 flex items-center justify-center text-xs font-black italic text-primary">
                                    {{ substr($name, 0, 1) }}</div>
                                <div>
                                    <div class="font-black text-slate-100 italic uppercase">{{ $name }}</div>
                                    <div class="text-[8px] text-slate-500 font-black uppercase tracking-widest">
                                        {{ $data[0] }}</div>
                                </div>
                            </td>
                            <td
                                class="px-6 py-5 text-right font-black text-primary text-2xl italic group-hover:scale-110 transition-transform">
                                {{ $data[1] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.public>