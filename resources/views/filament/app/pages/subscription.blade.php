<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Current Plan Card --}}
        <x-filament::section class="md:col-span-1">
            <x-slot name="heading">
                Estado Actual
            </x-slot>

            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Plan</p>
                    <p class="text-2xl font-extrabold text-primary-600">{{ $league->plan->label() }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Estado</p>
                    <span
                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $league->subscription_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($league->subscription_status) }}
                    </span>
                </div>

                @if($league->subscription_ends_at)
                <div>
                    <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Termina el</p>
                    <p class="font-medium">{{ $league->subscription_ends_at->format('d/m/Y') }}</p>
                </div>
                @endif
            </div>
        </x-filament::section>

        {{-- Limits visualization --}}
        <x-filament::section class="md:col-span-2">
            <x-slot name="heading">
                Límites y Uso
            </x-slot>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                {{-- Teams Limit --}}
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                    <p class="text-gray-600 font-medium mb-2">Equipos</p>
                    <div class="flex items-end space-x-2">
                        <span class="text-4xl font-bold">{{ $league->teams()->count() }}</span>
                        <span class="text-gray-400 text-xl font-medium">/ {{ $league->plan->maxTeams() ?? '∞' }}</span>
                    </div>
                    <div class="mt-4 w-full bg-gray-200 rounded-full h-2.5">
                        @php
                        $teamLimit = $league->plan->maxTeams();
                        $teamCount = $league->teams()->count();
                        $teamPercent = $teamLimit ? min(100, ($teamCount / $teamLimit) * 100) : 0;
                        @endphp
                        <div class="bg-primary-600 h-2.5 rounded-full" style="width: {{ $teamPercent }}%"></div>
                    </div>
                </div>

                {{-- Competitions Limit --}}
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                    <p class="text-gray-600 font-medium mb-2">Competiciones</p>
                    <div class="flex items-end space-x-2">
                        @php
                        $compCount = \App\Models\Competition::whereHas('category', function($q) use ($league) {
                        $q->where('league_id', $league->id);
                        })->count();
                        $compLimit = $league->plan->maxCompetitions();
                        $compPercent = $compLimit ? min(100, ($compCount / $compLimit) * 100) : 0;
                        @endphp
                        <span class="text-4xl font-bold">{{ $compCount }}</span>
                        <span class="text-gray-400 text-xl font-medium">/ {{ $compLimit ?? '∞' }}</span>
                    </div>
                    <div class="mt-4 w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-primary-600 h-2.5 rounded-full" style="width: {{ $compPercent }}%"></div>
                    </div>
                </div>
            </div>

            @if($league->plan === \App\Enums\Plan::FREE)
            <div class="mt-6 p-4 bg-indigo-50 border border-indigo-100 rounded-lg flex items-start space-x-3">
                <x-heroicon-o-information-circle class="h-6 w-6 text-indigo-600 flex-shrink-0" />
                <div>
                    <p class="text-indigo-800 font-bold">¡Mejora tu plan!</p>
                    <p class="text-indigo-700 text-sm">El plan gratuito es ideal para empezar, pero el Plan Pro
                        desbloquea hasta 5 competiciones y 20 equipos.</p>
                </div>
            </div>
            @endif
        </x-filament::section>

    </div>

    {{-- Plans overview --}}
    <div class="mt-12">
        <h3 class="text-xl font-bold mb-6">Planes Disponibles</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach(\App\Enums\Plan::cases() as $p)
            <div
                class="bg-white rounded-2xl shadow-sm border {{ $league->plan === $p ? 'border-primary-600 ring-2 ring-primary-500' : 'border-gray-200' }} p-8 relative overflow-hidden">
                @if($league->plan === $p)
                <div
                    class="absolute top-0 right-0 bg-primary-600 text-white px-4 py-1 text-xs font-bold rounded-bl-xl uppercase tracking-widest">
                    Actual
                </div>
                @endif
                <h4 class="text-lg font-bold text-gray-900 border-b pb-4 mb-4">{{ $p->label() }}</h4>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-center text-gray-600">
                        <x-heroicon-o-check-circle class="h-5 w-5 text-green-500 mr-2" />
                        {{ $p->maxCompetitions() ?? 'Ilimitadas' }} Competiciones
                    </li>
                    <li class="flex items-center text-gray-600">
                        <x-heroicon-o-check-circle class="h-5 w-5 text-green-500 mr-2" />
                        {{ $p->maxTeams() ?? 'Ilimitados' }} Equipos
                    </li>
                    <li class="flex items-center text-gray-600">
                        <x-heroicon-o-check-circle class="h-5 w-5 text-green-500 mr-2" />
                        Soporte {{ $p === \App\Enums\Plan::UNLIMITED ? 'Prioritario' : 'Estándar' }}
                    </li>
                </ul>
            </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>