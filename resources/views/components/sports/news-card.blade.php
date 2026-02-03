@props(['title', 'category' => 'News', 'image' => null, 'time' => '2h ago'])

<div
    class="group cursor-pointer flex flex-col h-full bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition duration-300 border border-transparent hover:border-gray-200">
    <div class="relative h-48 w-full overflow-hidden bg-gray-200">
        @if($image)
            <img src="{{ $image }}" alt="{{ $title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                <span class="text-4xl font-bold opacity-20">NEWS</span>
            </div>
        @endif
        <div
            class="absolute top-2 left-2 bg-brand-primary text-white text-[10px] font-bold px-2 py-1 uppercase rounded tracking-wide">
            {{ $category }}
        </div>
    </div>
    <div class="p-4 flex-1 flex flex-col">
        <h3 class="text-lg font-bold leading-tight text-gray-900 group-hover:text-brand-primary transition mb-2">
            {{ $title }}
        </h3>
        <div
            class="mt-auto pt-4 flex items-center justify-between text-xs text-gray-500 font-medium uppercase tracking-wide">
            <span>{{ $time }}</span>
            <span class="flex items-center text-brand-secondary">
                Read More
                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </span>
        </div>
    </div>
</div>