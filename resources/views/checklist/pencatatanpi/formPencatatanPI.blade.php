@extends('layouts.app')

@section('title', 'Form Pencatatan PI')
@section('content')
<div class="mx-auto p-2 sm:p-4 min-h-screen pt-5 sm:pt-20">
    @if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition class="bg-gradient-to-r from-blue-400 to-blue-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg border-l-4 border-blue-600 text-sm sm:text-base">
        <div class="flex items-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition class="bg-gradient-to-r from-red-400 to-red-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-xl mb-4 sm:mb-6 shadow-lg border-l-4 border-red-600 text-sm sm:text-base">
        <div class="flex items-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="font-bold">Error!</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Mobile: Card Layout --}}
    <div class="block lg:hidden space-y-3 mb-6">
        <div class="bg-white shadow-lg rounded-xl border border-gray-200 p-3">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Form Pencatatan PI</h3>
                    <p class="text-xs text-gray-500">Catatan aktivitas Prohibited Items</p>
                </div>
                <a href="{{ route('checklist.pencatatanpi.create') }}" class="bg-blue-500 text-white px-3 py-2 rounded-lg font-semibold text-sm hover:bg-blue-600 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah
                </a>
            </div>
        </div>

        @forelse($pencatatanPI ?? [] as $index => $item)
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-bold mr-2">
                        {{ $index + 1 }}
                    </span>
                    <div>
                        <h3 class="font-semibold text-gray-900 text-sm">{{ $item->name_person ?? '-' }}</h3>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->date)->format('d M Y') ?? '-'}}</p>
                    </div>
                </div>
                @if ($item->status == 'draft')
                <div class="flex space-x-1">
                    <a href="{{ route('checklist.pencatatanpi.edit', $item->id) }}"
                        class="p-1.5 bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200 transition-colors"
                        title="Edit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    <form action="{{ route('checklist.pencatatanpi.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors" title="Hapus">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <div class="space-y-2 text-xs">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p class="text-gray-500">Instansi</p>
                        <p class="font-medium">{{ $item->agency ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Jam Masuk</p>
                        <p class="font-medium">{{ $item->in_time ?? '-' }}</p>
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100">
                    <p class="text-gray-500">Item Details</p>
                    <div class="font-medium space-y-1 mt-1">
                        @foreach($item->details as $detail)
                        <div class="p-2 rounded-md bg-gray-50 border border-gray-200">
                            <p class="font-semibold">{{ $detail->jenis_pi }}</p>
                            <div class="flex justify-between text-xs mt-1">
                                <span>Jml Masuk: <span class="font-bold text-blue-600">{{ $detail->in_quantity }}</span></span>
                                <span>Jml Keluar: <span class="font-bold text-blue-600">{{ $detail->out_quantity ?? '-' }}</span></span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100">
                    <p class="text-gray-500 text-xs">Keterangan</p>
                    <p class="font-medium break-words">{{ $item->summary ?? '-' }}</p>
                </div>

                <div class="pt-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                        @if($item->status == 'draft') bg-yellow-100 text-yellow-800
                        @elseif($item->status == 'submitted') bg-blue-100 text-blue-800
                        @else bg-green-100 text-green-800 @endif">
                        {{ ucfirst($item->status ?? '-') }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-8 text-center">
            <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-base font-semibold text-gray-500 mb-1">Belum Ada Data</h3>
            <p class="text-xs text-gray-400">Data pencatatan akan muncul di sini setelah dibuat</p>
        </div>
        @endforelse
    </div>

    {{-- Desktop: Table Layout --}}
    <div class="hidden lg:block bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-4 sm:px-6 py-4 sm:py-6 text-white">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-1">Form Pencatatan PI</h3>
                    <p class="text-blue-100 text-sm sm:text-base">Catatan aktivitas Prohibited Items harian</p>
                </div>
                <a href="{{ route('checklist.pencatatanpi.create') }}" class="bg-white text-blue-600 hover:bg-blue-50 px-4 sm:px-6 py-2 sm:py-3 mt-3 sm:mt-0 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto text-sm sm:text-base">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Data
                </a>
            </div>
        </div>

        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-2">
                    <tr class="border-2">
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider align-top" rowspan="2">No</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider align-top" rowspan="2">Tanggal</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" colspan="2">Jam</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider align-top" rowspan="2">Nama Pemilik</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider align-top" rowspan="2">Instansi</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider align-top" rowspan="2">Jenis PI</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" colspan="2">Jumlah</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider align-top" rowspan="2">Ket</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider align-top" rowspan="2">Status</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider align-top" rowspan="2">Aksi</th>
                    </tr>
                    <tr class="border-2">
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Masuk</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Keluar</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Masuk</th>
                        <th class="border-2 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Keluar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pencatatanPI ?? [] as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-3 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-sm align-top">{{ $index + 1 }}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-sm align-top">{{ \Carbon\Carbon::parse($item->date)->format('d M Y') ?? '-'}}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-center text-sm align-top">{{ $item->in_time ?? '-' }}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-center text-sm align-top">{{ $item->out_time ?? '-' }}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-sm align-top">{{ $item->name_person ?? '-' }}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-sm align-top">{{ $item->agency ?? '-' }}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 text-sm align-top">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($item->details as $detail)
                                    <li>{{ $detail->jenis_pi }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 text-center text-sm align-top">
                            <ul class="list-none space-y-1">
                                @foreach($item->details as $detail)
                                    <li>{{ $detail->in_quantity }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 text-center text-sm align-top">
                            <ul class="list-none space-y-1">
                                @foreach($item->details as $detail)
                                    <li>{{ $detail->out_quantity ?? '-' }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 text-sm align-top">{{ $item->summary ?? '-' }}</td>
                        <td class="px-3 sm:px-4 py-2 sm:py-3 text-sm align-top">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                @if($item->status == 'draft') bg-yellow-100 text-yellow-800
                                @elseif($item->status == 'submitted') bg-blue-100 text-blue-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($item->status ?? '-') }}
                            </span>
                        </td>

                        <td class="px-3 sm:px-4 py-2 sm:py-3 flex justify-center space-x-1 align-top">
                            @if ($item->status == 'draft')
                            <a href="{{ route('checklist.pencatatanpi.edit', $item->id) }}"
                                class="p-1.5 sm:p-2 bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200 transition-colors duration-200"
                                title="Edit Pencatatan PI">
                                <svg class="w-3.5 sm:w-4 h-3.5 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('checklist.pencatatanpi.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 sm:p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200" title="Hapus Pencatatan PI">
                                    <svg class="w-3.5 sm:w-4 h-3.5 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                            @else
                            <span class="text-gray-400 text-xs italic">Terkirim</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center py-8 sm:py-12">
                            <p class="text-gray-500 text-base sm:text-lg">Belum ada data.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .max-w-6xl {
            max-width: 100%;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        .text-sm { font-size: 0.8125rem; }
        .text-xs { font-size: 0.75rem; }
        .text-base { font-size: 0.875rem; }
        .text-lg { font-size: 1.125rem; }
        .text-xl { font-size: 1.25rem; }
        .text-2xl { font-size: 1.5rem; }
    }
    @media (max-width: 640px) {
        .max-w-xs { max-width: 90%; }
    }
    @media print {
        .no-print { display: none !important; }
    }
    .transition-colors { transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out; }
    .transition-shadow { transition: box-shadow 0.2s ease-in-out; }
    .scrollbar-thin::-webkit-scrollbar { height: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection
