<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($this->getViewData()['plans'] as $plan)
            @php
                $isCurrent = $this->getViewData()['currentPlan'] === $plan->value; // Rough check, improved by checking price ID
                // Ideally passing detailed data from component
                $stripePriceId = $plan->stripePriceId();
                $isFree = $plan === \App\Enums\Plan::FREE;
            @endphp
            <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800 flex flex-col">
                <h3 class="text-xl font-bold mb-2">{{ $plan->label() }}</h3>
                <div class="flex-grow">
                    <ul class="list-disc pl-5 space-y-2 mb-4">
                        <li>{{ $plan->maxCompetitions() ?? 'Ilimitadas' }} Competiciones</li>
                        <li>{{ $plan->maxTeams() ?? 'Ilimitados' }} Equipos</li>
                        {{-- Add more features --}}
                    </ul>
                </div>

                <div class="mt-4">
                    @if($isFree)
                        <span class="block w-full text-center py-2 px-4 bg-gray-200 text-gray-700 rounded cursor-not-allowed">
                            Gratis
                        </span>
                    @else
                        <button
                            wire:click="subscribe('{{ $plan->value }}')"
                            wire:loading.attr="disabled"
                            class="w-full py-2 px-4 bg-primary-600 hover:bg-primary-700 text-white rounded transition"
                        >
                            {{ $isCurrent ? 'Plan Actual' : 'Suscribirse' }}
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if($this->getViewData()['isSubscribed'])
        <div class="mt-8">
            <h3 class="text-lg font-medium">Gestión de Suscripción</h3>
            <p class="text-sm text-gray-500 mb-4">Gestiona tu método de pago y facturas.</p>
            <button
                wire:click="manage"
                class="py-2 px-4 bg-gray-600 hover:bg-gray-700 text-white rounded"
            >
                Administrar Suscripción en Stripe
            </button>
        </div>
    @endif
</x-filament-panels::page>
