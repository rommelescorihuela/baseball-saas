<div class="space-y-8 pb-32">
    {{-- Broadcast Scoreboard (The "Bug") --}}
    <div
        class="glass-morphism rounded-full p-2 pr-8 flex items-center justify-between border-brand-teal/20 shadow-[0_0_30px_rgba(0,223,255,0.1)]">
        <div class="flex items-center space-x-6">
            {{-- Inning Indicator --}}
            <div
                class="bg-brand-navy rounded-full w-14 h-14 flex flex-col items-center justify-center border border-white/10">
                <div class="text-[10px] font-black uppercase text-brand-teal leading-none mb-0.5">
                    {{ $is_top_inning ? 'TOP' : 'BOT' }}</div>
                <div class="text-2xl font-black italic leading-none">{{ $inning }}</div>
            </div>

            {{-- Score --}}
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-3">
                    <span
                        class="text-xs font-black uppercase tracking-widest opacity-50">{{ substr($game->visitorTeam->name, 0, 3) }}</span>
                    <span class="text-3xl font-black italic">{{ $game->visitor_score ?? 0 }}</span>
                </div>
                <div class="w-px h-6 bg-white/10"></div>
                <div class="flex items-center space-x-3">
                    <span class="text-3xl font-black italic">{{ $game->home_score ?? 0 }}</span>
                    <span
                        class="text-xs font-black uppercase tracking-widest opacity-50">{{ substr($game->homeTeam->name, 0, 3) }}</span>
                </div>
            </div>
        </div>

        {{-- Counts & Bases --}}
        <div class="flex items-center space-x-8">
            {{-- Count (B-S) --}}
            <div class="flex items-center space-x-4 bg-white/5 py-2 px-6 rounded-full border border-white/5">
                <div class="flex flex-col items-center">
                    <span class="text-[8px] font-black uppercase tracking-widest text-gray-500">Balls</span>
                    <div class="flex space-x-1 mt-1">
                        @for($i = 1; $i <= 3; $i++)
                            <div
                                class="w-2 h-2 rounded-full {{ $balls >= $i ? 'bg-brand-teal shadow-[0_0_8px_rgba(0,229,255,1)]' : 'bg-white/10' }}">
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <span class="text-[8px] font-black uppercase tracking-widest text-gray-500">Strikes</span>
                    <div class="flex space-x-1 mt-1">
                        @for($i = 1; $i <= 2; $i++)
                            <div
                                class="w-2 h-2 rounded-full {{ $strikes >= $i ? 'bg-brand-orange shadow-[0_0_8px_rgba(255,110,64,1)]' : 'bg-white/10' }}">
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <span class="text-[8px] font-black uppercase tracking-widest text-gray-500">Outs</span>
                    <div class="flex space-x-1 mt-1">
                        @for($i = 1; $i <= 2; $i++)
                            <div class="w-2 h-2 rounded-full {{ $outs >= $i ? 'bg-red-500' : 'bg-white/10' }}"></div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- The Diamond --}}
            <div class="relative w-12 h-12 transform rotate-45 border-2 border-white/10">
                {{-- Second Base --}}
                <div
                    class="absolute -top-1.5 -left-1.5 w-3 h-3 border border-white/20 {{ $runner_on_second ? 'bg-brand-teal shadow-[0_0_10px_rgba(0,229,255,1)] border-none' : 'bg-transparent' }}">
                </div>
                {{-- First Base --}}
                <div
                    class="absolute -bottom-1.5 -left-1.5 w-3 h-3 border border-white/20 {{ $runner_on_first ? 'bg-brand-teal shadow-[0_0_10px_rgba(0,229,255,1)] border-none' : 'bg-transparent' }}">
                </div>
                {{-- Third Base --}}
                <div
                    class="absolute -top-1.5 -right-1.5 w-3 h-3 border border-white/20 {{ $runner_on_third ? 'bg-brand-teal shadow-[0_0_10px_rgba(0,229,255,1)] border-none' : 'bg-transparent' }}">
                </div>
                {{-- Home --}}
                <div class="absolute -bottom-1.5 -right-1.5 w-3 h-3 border border-white/20 bg-white/5"></div>
            </div>
        </div>
    </div>

    {{-- Matchup Header --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="glass-morphism rounded-3xl p-6 border-white/5 group hover:border-brand-teal/30 transition">
            <label class="block text-[10px] font-black uppercase tracking-widest text-brand-teal mb-4">Bateador <span
                    class="text-white/30 italic">({{ $this->offensiveTeam->name }})</span></label>
            <div class="relative">
                <select wire:model="batter_id"
                    class="w-full bg-brand-navy border-white/10 text-white rounded-2xl py-4 pl-6 pr-10 appearance-none font-black uppercase italic tracking-tighter text-xl focus:ring-brand-teal focus:border-brand-teal">
                    <option value="">-- Seleccionar --</option>
                    @foreach($offensivePlayers as $player)
                        <option value="{{ $player->id }}">{{ $player->name }} {{ $player->last_name }}
                            (#{{ $player->number }})</option>
                    @endforeach
                </select>
                <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-brand-teal">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="glass-morphism rounded-3xl p-6 border-white/5 group hover:border-brand-orange/30 transition">
            <label class="block text-[10px] font-black uppercase tracking-widest text-brand-orange mb-4">Lanzador <span
                    class="text-white/30 italic">({{ $this->defensiveTeam->name }})</span></label>
            <div class="relative">
                <select wire:model="pitcher_id"
                    class="w-full bg-brand-navy border-white/10 text-white rounded-2xl py-4 pl-6 pr-10 appearance-none font-black uppercase italic tracking-tighter text-xl focus:ring-brand-orange focus:border-brand-orange">
                    <option value="">-- Seleccionar --</option>
                    @foreach($defensivePlayers as $player)
                        <option value="{{ $player->id }}">{{ $player->name }} {{ $player->last_name }}
                            (#{{ $player->number }})</option>
                    @endforeach
                </select>
                <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-brand-orange">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Controls Hub --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Pitch Section --}}
        <div class="space-y-4">
            <h5 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500 mb-6 flex items-center">
                <span class="w-8 h-px bg-white/10 mr-3"></span> Fast Actions
            </h5>
            <div class="grid grid-cols-1 gap-4">
                <button wire:click="registerEvent('pitch', 'ball')"
                    class="group relative glass-morphism rounded-2xl p-6 text-left hover:border-brand-teal/50 transition active:scale-95">
                    <div
                        class="absolute right-6 top-1/2 -translate-y-1/2 text-4xl font-black italic opacity-5 group-hover:opacity-20 transition">
                        BOLA</div>
                    <div class="text-brand-teal font-black uppercase tracking-widest text-[10px] mb-1">Acción</div>
                    <div class="text-2xl font-black italic italic">BOLA</div>
                </button>
                <button wire:click="registerEvent('pitch', 'strike')"
                    class="group relative glass-morphism rounded-2xl p-6 text-left hover:border-brand-orange/50 transition active:scale-95">
                    <div
                        class="absolute right-6 top-1/2 -translate-y-1/2 text-4xl font-black italic opacity-5 group-hover:opacity-20 transition">
                        STRIKE</div>
                    <div class="text-brand-orange font-black uppercase tracking-widest text-[10px] mb-1">Acción</div>
                    <div class="text-2xl font-black italic italic">STRIKE</div>
                </button>
                <button wire:click="registerEvent('pitch', 'foul')"
                    class="group relative glass-morphism rounded-2xl p-6 text-left hover:border-white/30 transition active:scale-95">
                    <div class="text-gray-400 font-black uppercase tracking-widest text-[10px] mb-1">Acción</div>
                    <div class="text-2xl font-black italic italic">FOUL</div>
                </button>
            </div>
        </div>

        {{-- Bateo (Hits) Section --}}
        <div class="lg:col-span-2 space-y-4">
            <h5 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500 mb-6 flex items-center">
                <span class="w-8 h-px bg-white/10 mr-3"></span> In-Play Results
            </h5>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach(['1b' => 'Sencillo', '2b' => 'Doble', '3b' => 'Triple', 'hr' => 'HOME RUN'] as $key => $label)
                    <button wire:click="registerEvent('play', '{{ $key }}')"
                        class="glass-morphism rounded-2xl p-8 hover:bg-brand-teal hover:text-brand-navy transition active:scale-95 group">
                        <div class="text-3xl font-black italic mb-2 uppercase">{{ $key }}</div>
                        <div class="text-[8px] font-black uppercase tracking-widest opacity-50">{{ $label }}</div>
                    </button>
                @endforeach
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mt-8">
                <button wire:click="registerEvent('play', 'out')"
                    class="glass-morphism rounded-2xl py-6 hover:bg-red-600 transition font-black uppercase tracking-widest text-xs italic text-white uppercase">OUT
                    REGULAR</button>
                <button wire:click="registerEvent('play', 'walk')"
                    class="glass-morphism rounded-2xl py-6 hover:bg-brand-teal hover:text-brand-navy transition font-black uppercase tracking-widest text-xs italic text-white uppercase">WALK
                    (BB)</button>
                <button wire:click="registerEvent('play', 'error')"
                    class="glass-morphism rounded-2xl py-6 hover:bg-brand-orange transition font-black uppercase tracking-widest text-xs italic text-white uppercase">ERROR
                    (E)</button>
            </div>

            <div class="mt-12 flex justify-between items-center opacity-30">
                <div class="text-[10px] font-black uppercase tracking-[0.5em]">Game ID: {{ $game->id }}</div>
                <button wire:confirm="¿Seguro que desea finalizar el partido?" wire:click="finishGame"
                    class="text-[10px] font-black uppercase tracking-[0.2em] hover:text-red-500 transition">Finalizar
                    Encuentro</button>
            </div>
        </div>
    </div>
</div>