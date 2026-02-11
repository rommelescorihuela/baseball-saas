<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Scoreboard --}}
        <div class="md:col-span-3 bg-white p-4 rounded-lg shadow dark:bg-gray-800">
            <div class="flex justify-between items-center">
                <div class="text-center">
                    <h3 class="text-xl font-bold">{{ $record->homeTeam->name }}</h3>
                    <span class="text-4xl font-mono">{{ $record->home_score }}</span>
                </div>
                <div class="text-center">
                    <span class="text-gray-500">VS</span>
                    <div class="mt-2 text-sm bg-gray-100 px-2 py-1 rounded dark:bg-gray-700">
                        Inning: {{-- Placeholder --}} 1 Top
                    </div>
                </div>
                <div class="text-center">
                    <h3 class="text-xl font-bold">{{ $record->visitorTeam->name }}</h3>
                    <span class="text-4xl font-mono">{{ $record->visitor_score }}</span>
                </div>
            </div>

            <div class="mt-4 flex justify-center gap-4 text-sm font-mono">
                <div>B: 0</div>
                <div>S: 0</div>
                <div>O: 0</div>
            </div>
        </div>

        {{-- Controls placeholder --}}
        <div class="md:col-span-3">
            <div class="p-4 bg-gray-50 rounded border border-gray-200 dark:bg-gray-900 dark:border-gray-700">
                <livewire:game-scoring :game="$record" />
            </div>
        </div>
    </div>nt-panels::page>