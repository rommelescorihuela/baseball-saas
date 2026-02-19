<div>
    <div class="space-y-4">
        <h4 class="text-lg font-bold">Scoring Controls</h4>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Batter ({{
                    $this->offensiveTeam->name }})</label>
                <select wire:model="batter_id"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                    <option value="">Select Batter</option>
                    @foreach($offensivePlayers as $player)
                    <option value="{{ $player->id }}">{{ $player->name }} {{ $player->last_name }} (#{{ $player->number
                        }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pitcher ({{
                    $this->defensiveTeam->name }})</label>
                <select wire:model="pitcher_id"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                    <option value="">Select Pitcher</option>
                    @foreach($defensivePlayers as $player)
                    <option value="{{ $player->id }}">{{ $player->name }} {{ $player->last_name }} (#{{ $player->number
                        }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <button wire:click="registerEvent('pitch', 'ball')"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Ball
            </button>
            <button wire:click="registerEvent('pitch', 'strike')"
                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Strike
            </button>
            <button wire:click="registerEvent('pitch', 'foul')"
                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                Foul
            </button>
            <button wire:click="registerEvent('play', 'out')"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Out
            </button>
            <button wire:click="registerEvent('play', 'hit')"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Hit
            </button>
        </div>

        <div class="mt-4">
            <p class="text-sm text-gray-600">Game ID: {{ $game->id }}</p>
        </div>
    </div>
</div>