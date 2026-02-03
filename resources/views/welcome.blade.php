<x-sports-layout>
    <!-- Featured Hero Section -->
    <section class="relative bg-gray-900 text-white">
        @if($heroArticle)
            <div class="absolute inset-0 bg-cover bg-center opacity-60"
                style="background-image: url('{{ $heroArticle->image_url ?? asset('images/hero-bg.png') }}');">
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/10"></div>

            <div class="relative container mx-auto px-4 py-24 md:py-32 flex flex-col justify-end min-h-[500px]">
                <span
                    class="bg-brand-secondary text-white text-xs font-bold px-3 py-1 uppercase tracking-widest rounded w-fit mb-4">{{ $heroArticle->category }}</span>
                <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-4 max-w-4xl">
                    {{ $heroArticle->title }}
                </h1>
                <p class="text-xl md:text-2xl font-medium text-gray-200 mb-8 max-w-2xl">
                    {{ Str::limit($heroArticle->content, 150) }}
                </p>
                <div class="flex space-x-4">
                    <a href="#"
                        class="bg-white text-brand-primary font-bold py-3 px-8 rounded-full uppercase tracking-wide hover:bg-gray-100 transition shadow-lg">
                        Read Story
                    </a>
                    <a href="#"
                        class="bg-transparent border-2 border-white text-white font-bold py-3 px-8 rounded-full uppercase tracking-wide hover:bg-white/10 transition">
                        Full Schedule
                    </a>
                </div>
            </div>
        @endif
    </section>

    <!-- Content Grid -->
    <div class="container mx-auto px-4 py-12">
        <div class="flex flex-col lg:flex-row gap-12">

            <!-- Main News Column -->
            <div class="lg:w-2/3">
                <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-2">
                    <h2 class="text-2xl font-extrabold text-brand-primary uppercase tracking-tight">Top Headlines</h2>
                    <a href="#" class="text-sm font-bold text-brand-secondary hover:underline uppercase">View All
                        News</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                    @foreach($newsGrid as $article)
                        <x-sports.news-card 
                            :title="$article->title" 
                            :time="$article->published_at->diffForHumans()"
                            :category="$article->category"
                            :image="$article->image_url" />
                    @endforeach
                </div>
            </div>

            <!-- Sidebar (Standings / Stats) -->
            <div class="lg:w-1/3">
                <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-2">
                    <h2 class="text-2xl font-extrabold text-brand-primary uppercase tracking-tight">Standings</h2>
                    <a href="#" class="text-sm font-bold text-brand-secondary hover:underline uppercase">Full
                        Standings</a>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-8">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                        <span class="font-bold text-gray-700 text-sm">AL East</span>
                    </div>
                    <table class="w-full text-sm text-center">
                        <thead class="bg-white text-gray-500 text-xs uppercase font-medium border-b">
                            <tr>
                                <th class="px-3 py-2 text-left">Team</th>
                                <th class="px-2 py-2">W</th>
                                <th class="px-2 py-2">L</th>
                                <th class="px-2 py-2">Pct</th>
                                <th class="px-2 py-2">GB</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($standings as $stat)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-3 text-left font-bold text-gray-900 flex items-center space-x-2">
                                        <span class="w-2 h-2 rounded-full"
                                            style="background-color: {{ $stat['team']->primary_color ?? '#000' }};"></span>
                                        <span>{{ $stat['team']->category->name ?? '' }} {{ $stat['team']->name }}</span>
                                    </td>
                                    <td class="font-bold">{{ $stat['wins'] }}</td>
                                    <td>{{ $stat['losses'] }}</td>
                                    <td>{{ $stat['pct'] }}</td>
                                    <td>{{ $stat['gb'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Video / Promo -->
                <div class="bg-gray-900 rounded-lg p-6 text-center text-white">
                    <h3 class="text-xl font-bold mb-2">Watch Live Games</h3>
                    <p class="text-gray-400 text-sm mb-4">Stream every out-of-market game live or on-demand.</p>
                    <button
                        class="w-full bg-brand-primary hover:bg-blue-800 text-white font-bold py-2 rounded uppercase tracking-wide transition">
                        Subscribe Now
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-sports-layout>