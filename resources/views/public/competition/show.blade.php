<x-layouts.public>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $competition->name }}</h1>
                <p class="text-gray-500">{{ $competition->season->name }}</p>
            </div>
            <div class="space-x-4">
                <a href="{{ route('public.competition.calendar', $competition) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    View Calendar
                </a>
            </div>
        </div>

        {{-- Standings Mockup --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="padding-6 border-b border-gray-200 p-6">
                <h2 class="text-lg font-medium text-gray-900">Participating Teams</h2>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($competition->teams as $team)
                <li class="p-4 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center">
                        <div
                            class="h-10 w-10 flex-shrink-0 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                            {{ substr($team->name, 0, 1) }}
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $team->name }}</div>
                            <div class="text-sm text-gray-500">{{ $team->city ?? 'Unknown City' }}</div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500">
                        <!-- Placeholder for stats -->
                        0 W - 0 L
                    </div>
                </li>
                @empty
                <li class="p-4 text-center text-gray-500">No teams registered yet.</li>
                @endforelse
            </ul>
        </div>

    </div>
</x-layouts.public>