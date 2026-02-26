<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($this->getViewData()['plans'] as $plan)
            @php
                $isCurrent = $this->getViewData()['currentPlan'] === $plan;
                $isFree = $plan === \App\Enums\Plan::FREE;
            @endphp
            <div @class([
                'p-6 rounded-lg shadow flex flex-col',
                'bg-primary-50 border-2 border-primary-500 dark:bg-primary-900/10' => $isCurrent,
                'bg-white dark:bg-gray-800' => !$isCurrent,
            ])>
                <h3 class="text-xl font-bold mb-2">{{ $plan->label() }}</h3>
                <div class="flex-grow">
                    <ul class="list-disc pl-5 space-y-2 mb-4">
                        <li>{{ $plan->maxCompetitions() ?? 'Ilimitadas' }} Competiciones</li>
                        <li>{{ $plan->maxTeams() ?? 'Ilimitados' }} Equipos</li>
                    </ul>
                </div>

                <div class="mt-4">
                    @if($isCurrent)
                        <span class="block w-full text-center py-2 px-4 bg-primary-100 text-primary-700 rounded font-medium">
                            Plan Actual
                        </span>
                    @else
                        <div class="text-center text-sm text-gray-500 italic">
                            Contactar soporte para cambiar de plan
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 p-6 bg-white rounded-lg shadow dark:bg-gray-800 border-l-4 border-primary-500">
        <h3 class="text-lg font-bold mb-4">Instrucciones de Pago (Manual)</h3>
        <p class="mb-4">Para actualizar su plan o renovar su suscripción, por favor realice una transferencia bancaria a
            la siguiente cuenta:</p>

        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded mb-4 font-mono text-sm">
            <p><strong>Banco:</strong> Banco Mercantil</p>
            <p><strong>Titular:</strong> Baseball SaaS C.A.</p>
            <p><strong>RIF:</strong> J-12345678-9</p>
            <p><strong>Cuenta:</strong> 0105-XXXX-XXXX-XXXX-XXXX</p>
            <p><strong>Pago Móvil:</strong> 0412-XXX-XXXX (Banco Mercantil)</p>
        </div>

        <p class="mb-4">Una vez realizado el pago, envíe el comprobante por WhatsApp al <strong>+58
                412-XXX-XXXX</strong> junto con el nombre de su liga:
            <strong>{{ \Filament\Facades\Filament::getTenant()->name }}</strong>.
        </p>

        <div class="flex items-center space-x-2 text-sm text-gray-600">
            <x-heroicon-o-information-circle class="w-5 h-5 text-primary-500" />
            <span>Un administrador validará su pago y activará los beneficios en menos de 24 horas.</span>
        </div>
    </div>
</x-filament-panels::page>