<x-layouts.public title="{{ $competition->name }} - Standings">
    <!-- Standings Header (Byte-Perfect from Stitch) -->
    <div class="px-6 pt-6 pb-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display text-4xl font-black text-white italic uppercase tracking-tighter leading-none">
                Posiciones</h2>
            <div class="flex gap-2">
                <button class="size-10 rounded-xl glass flex items-center justify-center text-primary"><span
                        class="material-symbols-outlined text-lg">share</span></button>
            </div>
        </div>
        <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1 border border-primary/20">
            <span
                class="text-[10px] font-black uppercase tracking-[0.2em] text-primary">{{ $competition->season->name }}</span>
        </div>

        <div class="flex items-center gap-4 overflow-x-auto no-scrollbar pb-2">
            <button
                class="px-5 py-2.5 rounded-xl bg-primary text-background-dark text-[10px] font-black uppercase tracking-widest shadow-lg shadow-primary/30">Regular</button>
            <button
                class="px-5 py-2.5 rounded-xl glass text-slate-400 text-[10px] font-black uppercase tracking-widest">Sede</button>
            <button
                class="px-5 py-2.5 rounded-xl glass text-slate-400 text-[10px] font-black uppercase tracking-widest">Interligas</button>
        </div>
    </div>

    <!-- Standings Table (Byte-Perfect Structure from Stitch) -->
    <div class="mt-4">
        <div class="px-6 py-2 bg-slate-900/60 border-y border-white/5">
            <div class="flex py-3 text-[10px] font-black text-slate-500 uppercase tracking-[0.3em]">
                <div class="w-10">#</div>
                <div class="flex-1">Equipo</div>
                <div class="w-10 text-center">JJ</div>
                <div class="w-10 text-center">G</div>
                <div class="w-10 text-center">P</div>
                <div class="w-14 text-center text-primary">.%</div>
                <div class="w-10 text-center">DIF</div>
            </div>
        </div>

        <!-- Standings List -->
        <div class="flex flex-col bg-background-dark">
            @forelse($standings as $index => $stat)
                <!-- Team Row (Adaptive Stitch Style) -->
                <div
                    class="flex items-center px-6 py-5 border-b border-white/5 transition duration-300 {{ $index == 0 ? 'bg-primary/5' : '' }} group hover:bg-white/5">
                    <div class="w-10 text-xl font-black italic {{ $index == 0 ? 'text-primary' : 'text-slate-700' }}">
                        {{ $index + 1 }}</div>
                    <div class="flex-1 flex items-center gap-4">
                        <div
                            class="size-10 rounded-xl bg-brand-navy border border-white/10 flex items-center justify-center text-lg font-black text-white italic uppercase shadow-xl group-hover:border-primary/50 transition">
                            {{ substr($stat['team']['name'] ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <p
                                class="text-base font-black text-white italic uppercase leading-none mb-1 group-hover:text-primary transition">
                                {{ $stat['team']['name'] ?? 'Unknown' }}</p>
                            <p class="text-[8px] text-slate-600 font-black uppercase tracking-widest">ROSTER ACTIVE</p>
                        </div>
                    </div>

                    <div class="w-10 text-center text-sm font-black text-slate-400 italic">
                        {{ $stat['wins'] + $stat['losses'] }}</div>
                    <div class="w-10 text-center text-lg font-black italic text-white">{{ $stat['wins'] }}</div>
                    <div class="w-10 text-center text-lg font-black italic text-slate-500">{{ $stat['losses'] }}</div>
                    <div class="w-14 text-center text-base font-black text-primary italic font-mono">
                        {{ number_format($stat['pct'], 3) }}</div>
                    <div
                        class="w-10 text-center text-[10px] font-black italic font-mono {{ ($stat['runs_for'] - $stat['runs_against']) >= 0 ? 'text-green-500' : 'text-slate-600' }}">
                        {{ ($stat['runs_for'] - $stat['runs_against']) > 0 ? '+' : '' }}{{ $stat['runs_for'] - $stat['runs_against'] }}
                    </div>
                </div>
            @empty
                <div class="py-40 text-center glass opacity-10 italic font-black uppercase tracking-[0.5em] text-[10px]">
                    No Data Transmitted
                </div>
            @endforelse
        </div>
    </div>

    <!-- Power Ranking Indicator (Decoration) -->
    <div class="px-6 py-12 mb-20">
        <div class="glass rounded-3xl p-8 border-primary/20 bg-primary/5">
            <h4 class="text-primary font-black uppercase italic tracking-widest text-[10px] mb-2">Pro Analytics Feed
            </h4>
            <p class="text-white text-lg font-black italic uppercase leading-tight mb-4 tracking-tighter">
                DiamondOS precision engine is calculating real-time probabilities for this season.
            </p>
            <div class="flex items-center gap-4">
                <div class="h-1 flex-1 bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-primary w-[72%] shadow-[0_0_10px_rgba(6,224,249,0.5)]"></div>
                </div>
                <span class="text-primary font-black italic text-sm font-mono">72%</span>
            </div>
        </div>
    </div>
</x-layouts.public>