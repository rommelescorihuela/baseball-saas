<x-sports-layout>
    <!-- Featured Hero Section -->
    <section class="relative bg-gray-900 text-white">
        <div class="absolute inset-0 bg-cover bg-center opacity-60"
            style="background-image: url('{{ asset('images/hero-bg.png') }}');">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/10"></div>

        <div class="relative container mx-auto px-4 py-24 md:py-32 flex flex-col justify-end min-h-[500px]">
            <span
                class="bg-brand-secondary text-white text-xs font-bold px-3 py-1 uppercase tracking-widest rounded w-fit mb-4">Trending
                Now</span>
            <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-4 max-w-4xl">
                Opening Day Approaches: <br>Top 5 Prospects to Watch This Season
            </h1>
            <p class="text-xl md:text-2xl font-medium text-gray-200 mb-8 max-w-2xl">
                The future of the league is here. Get to know the young stars ready to make an impact.
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
                    <x-sports.news-card title="Yankees maintain dominance in AL East standing" time="45m ago"
                        category="Recap" image="https://placehold.co/600x400/0f172a/FFF?text=Recap" />
                    <x-sports.news-card title="Ohtani's historic run continues with 45th HR" time="2h ago"
                        category="Highlight" />
                    <x-sports.news-card title="Trade Deadline Winners and Losers" time="5h ago" category="Analysis" />
                    <x-sports.news-card title="Developing: Pitcher injury report update" time="6h ago"
                        category="Injury" />
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
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3 text-left font-bold text-gray-900 flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-brand-primary"></span>
                                    <span>NYY</span>
                                </td>
                                <td class="font-bold">45</td>
                                <td>20</td>
                                <td>.692</td>
                                <td>-</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3 text-left font-bold text-gray-900 flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-red-600"></span>
                                    <span>BOS</span>
                                </td>
                                <td class="font-bold">38</td>
                                <td>27</td>
                                <td>.585</td>
                                <td>7.0</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3 text-left font-bold text-gray-900 flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                                    <span>TB</span>
                                </td>
                                <td class="font-bold">35</td>
                                <td>30</td>
                                <td>.538</td>
                                <td>10.0</td>
                            </tr>
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