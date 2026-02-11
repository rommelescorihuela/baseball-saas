@php
/** @var \App\Models\Game $record */
@endphp

<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Visitor Team Stats --}}
        <div>
            <h3 class="text-xl font-bold mb-4">{{ $record->visitorTeam->name }} Box Score</h3>
            <div class="overflow-x-auto border rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-3 py-2">Player</th>
                            <th scope="col" class="px-2 py-2">Pos</th>
                            <th scope="col" class="px-2 py-2">AB</th>
                            <th scope="col" class="px-2 py-2">R</th>
                            <th scope="col" class="px-2 py-2">H</th>
                            <th scope="col" class="px-2 py-2">RBI</th>
                            <th scope="col" class="px-2 py-2">BB</th>
                            <th scope="col" class="px-2 py-2">SO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->visitorStats as $stat)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $stat->player->name }} {{ $stat->player->last_name }}
                            </td>
                            <td class="px-2 py-2">{{ $stat->player->position }}</td>
                            <td class="px-2 py-2">{{ $stat->ab }}</td>
                            <td class="px-2 py-2">{{ $stat->r }}</td>
                            <td class="px-2 py-2">{{ $stat->h }}</td>
                            <td class="px-2 py-2">{{ $stat->rbi }}</td>
                            <td class="px-2 py-2">{{ $stat->bb }}</td>
                            <td class="px-2 py-2">{{ $stat->so }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Home Team Stats --}}
        <div>
            <h3 class="text-xl font-bold mb-4">{{ $record->homeTeam->name }} Box Score</h3>
            <div class="overflow-x-auto border rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-3 py-2">Player</th>
                            <th scope="col" class="px-2 py-2">Pos</th>
                            <th scope="col" class="px-2 py-2">AB</th>
                            <th scope="col" class="px-2 py-2">R</th>
                            <th scope="col" class="px-2 py-2">H</th>
                            <th scope="col" class="px-2 py-2">RBI</th>
                            <th scope="col" class="px-2 py-2">BB</th>
                            <th scope="col" class="px-2 py-2">SO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->homeStats as $stat)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $stat->player->name }} {{ $stat->player->last_name }}
                            </td>
                            <td class="px-2 py-2">{{ $stat->player->position }}</td>
                            <td class="px-2 py-2">{{ $stat->ab }}</td>
                            <td class="px-2 py-2">{{ $stat->r }}</td>
                            <td class="px-2 py-2">{{ $stat->h }}</td>
                            <td class="px-2 py-2">{{ $stat->rbi }}</td>
                            <td class="px-2 py-2">{{ $stat->bb }}</td>
                            <td class="px-2 py-2">{{ $stat->so }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-filament-panels::page>