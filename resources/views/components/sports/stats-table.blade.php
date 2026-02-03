@props(['headers', 'rows'])

<div class="overflow-x-auto shadow-sm rounded-lg border border-gray-200">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-800 text-white uppercase text-xs font-bold tracking-wider">
            <tr>
                @foreach($headers as $header)
                    <th class="px-4 py-3 whitespace-nowrap">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            {{ $slot }}
        </tbody>
    </table>
</div>