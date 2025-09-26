<!-- SECTION PERSONIL BERTUGAS -->
<div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
    <div class="bg-gradient-to-r from-sky-500 to-indigo-600 px-4 py-4 sm:px-6 text-white">
        <h3 class="text-xl sm:text-2xl font-bold">Personil Bertugas</h3>
    </div>

    <!-- Desktop Table View Personil -->
    <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama</th>
                    <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Klasifikasi</th>
                    <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($personil ?? [] as $index => $item)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-5 py-4 text-sky-600 font-bold">{{ $index + 1 }}</td>
                    <td class="px-5 py-4">{{ $item->user->name ?? 'N/A' }}</td>
                    <td class="px-5 py-4">{{ $item->classification ?? 'N/A' }}</td>
                    <td class="px-5 py-4">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($item->description == 'hadir') bg-green-100 text-green-800
                            @elseif($item->description == 'izin') bg-yellow-100 text-yellow-800
                            @elseif($item->description == 'sakit') bg-red-100 text-red-800
                            @elseif($item->description == 'cuti') bg-blue-100 text-blue-800
                            @endif">
                            {{ ucfirst($item->description) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-12">
                        <p class="text-gray-500 text-lg">Belum ada data personil dari logbook pos jaga hari ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
