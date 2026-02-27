<x-layouts.public title="{{ $seoTitle }}">
    <!-- Calendar Header -->
    <div class="px-6 pt-6 pb-4">
        <div class="flex items-center justify-between mb-2">
            <a href="{{ route('public.competition.show', $competition) }}"
                class="flex size-10 items-center justify-center rounded-full glass text-slate-400 hover:text-primary transition">
                <span class="material-symbols-outlined font-bold text-xl">arrow_back</span>
            </a>
            <div class="flex gap-2">
                <button class="size-10 rounded-xl glass flex items-center justify-center text-primary"><span
                        class="material-symbols-outlined text-lg">filter_alt</span></button>
            </div>
        </div>
        <h2 class="font-display text-4xl font-black text-white italic uppercase tracking-tighter leading-none mt-4">
            {{ $competition->name }}</h2>
        <div class="mt-2 inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1 border border-primary/20">
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-primary">Calendario Oficial</span>
        </div>
    </div>

    <!-- Games Feed (Stitch Adaptive) -->
    <div class="px-6 py-4 space-y-4 mb-20">
        @forelse($games as $game)
            <div
                class="rounded-3xl glass p-5 relative overflow-hidden border-white/5 group hover:bg-white/5 transition-all">
                @if($game->status == 'live')
                    <div
                        class="absolute top-0 right-0 px-4 py-1.5 bg-accent/20 text-accent text-[8px] font-black uppercase tracking-[0.3em] rounded-bl-2xl border-l border-b border-accent/30 animate-pulse">
                        EN VIVO
                    </div>
                @elseif($game->status == 'finished')
                    <div
                        class="absolute top-0 right-0 px-4 py-1.5 bg-slate-800/80 text-slate-400 text-[8px] font-black uppercase tracking-[0.3em] rounded-bl-2xl border-l border-b border-white/5">
                        FINALIZADO
                    </div>
                @else
                    <div
                        class="absolute top-0 right-0 px-4 py-1.5 bg-primary/10 text-primary text-[8px] font-black uppercase tracking-[0.3em] rounded-bl-2xl border-l border-b border-primary/20">
                        {{ $game->start_time->format('d M') }}
                    </div>
                @endif

                <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">
                    {{ $game->start_time->format('H:i') }} • {{ $game->category->name }}
                </div>

                <div class="flex items-center justify-between mb-4">
                    <div class="flex flex-col items-center gap-2">
                        <div
                            class="size-14 rounded-full bg-brand-navy border border-white/10 flex items-center justify-center text-xl font-black italic shadow-inner">
                            {{ substr($game->homeTeam->name, 0, 2) }}
                        </div>
                        <span
                            class="text-[9px] font-black text-slate-300 uppercase tracking-widest text-center w-20 truncate">{{ $game->homeTeam->name }}</span>
                    </div>

                    <div class="flex flex-col items-center px-4">
                        @if($game->status === 'finished' || $game->status === 'live')
                            <div class="text-3xl font-black tracking-tighter text-white italic">
                                {{ $game->home_score }}<span class="text-slate-600 mx-2">—</span>{{ $game->visitor_score }}
                            </div>
                        @else
                            <div class="text-xl font-black tracking-tighter text-slate-600 italic">VS</div>
                        @endif
                    </div>

                    <div class="flex flex-col items-center gap-2">
                        <div
                            class="size-14 rounded-full bg-brand-navy border border-white/10 flex items-center justify-center text-xl font-black italic shadow-inner">
                            {{ substr($game->visitorTeam->name, 0, 2) }}
                        </div>
                        <span
                            class="text-[9px] font-black text-slate-300 uppercase tracking-widest text-center w-20 truncate">{{ $game->visitorTeam->name }}</span>
                    </div>
                </div>

                <div
                    class="pt-4 border-t border-white/5 flex justify-center text-[9px] font-bold text-slate-500 tracking-[0.2em] uppercase">
                    <div class="flex items-center gap-1.5"><span
                            class="material-symbols-outlined text-[14px]">stadium</span>
                        {{ $game->location ?? 'Estadio Por Definir' }}</div>
                </div>
            </div>
        @empty
            <div
                class="py-20 text-center glass rounded-3xl opacity-40 italic font-black uppercase tracking-widest text-[10px]">
                No hay juegos programados.
            </div>
        @endforelse

        <!-- Pagination -->
        <div class="mt-8">
            {{ $games->links() }}
        </div>
    </div>
</x-layouts.public>