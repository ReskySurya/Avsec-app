@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 lg:pt-20">
    <h1 class="text-xl font-bold mb-4">Detail Logbook Rotasi</h1>

    {{-- Informasi Logbook Utama --}}
    <div class="bg-white p-4 shadow rounded mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p><span class="font-semibold">Tanggal:</span> {{ $logbook->date ? $logbook->date->format('d/m/Y') : '-' }}</p>
                <p><span class="font-semibold">Status:</span>
                    <span class="px-2 py-1 text-xs rounded
                        @if($logbook->status === 'approved') bg-green-100 text-green-800
                        @elseif($logbook->status === 'submitted') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($logbook->status) ?? '-' }}
                    </span>
                </p>
            </div>
            <div>
                <p><span class="font-semibold">Dibuat oleh:</span> {{ $logbook->creator?->display_name ?? '-' }}</p>
                @if($logbook->approved_by)
                    <p><span class="font-semibold">Diselesaikan oleh:</span> {{ $logbook->approver?->display_name ?? '-' }}</p>
                @endif
            </div>
        </div>
        @if($logbook->approved_at)
            <p class="mt-2"><span class="font-semibold">Tanggal Persetujuan:</span> {{ $logbook->approved_at?->format('d/m/Y H:i') ?? '-' }}</p>
        @endif
        @if($logbook->notes)
            <p class="mt-2"><span class="font-semibold">Catatan:</span> {{ $logbook->notes }}</p>
        @endif
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden md:block bg-white p-4 shadow rounded">
        <div class="overflow-x-auto">
            <table class="min-w-full border text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-2 whitespace-nowrap">Start</th>
                        <th class="border p-2 whitespace-nowrap">End</th>
                        <th class="border p-2 whitespace-nowrap">Pengatur Flow</th>
                        <th class="border p-2 whitespace-nowrap">Operator X-Ray</th>
                        <th class="border p-2 whitespace-nowrap">Pemeriksaan Manual Bagasi</th>
                        <th class="border p-2 whitespace-nowrap">Reunited</th>
                        <th class="border p-2 whitespace-nowrap">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logbook->details as $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="border p-2">{{ $detail->start ?? '-' }}</td>
                            <td class="border p-2">{{ $detail->end ?? '-' }}</td>
                            <td class="border p-2">{{ $detail->pengaturFlowOfficer?->display_name ?? '-' }}</td>
                            <td class="border p-2">{{ $detail->operatorXrayOfficer?->display_name ?? '-' }}</td>
                            <td class="border p-2">{{ $detail->manualBagasiOfficer?->display_name ?? '-' }}</td>
                            <td class="border p-2">{{ $detail->reunitedOfficer?->display_name ?? '-' }}</td>
                            <td class="border p-2">{{ $detail->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-gray-500 p-4">Tidak ada detail logbook</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Card View --}}
    <div class="md:hidden space-y-4">
        @forelse($logbook->details as $index => $detail)
            <div class="bg-white p-4 shadow rounded">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-lg">Detail #{{ $index + 1 }}</h3>
                    <div class="text-sm text-gray-600">
                        {{ $detail->start ?? '-' }} - {{ $detail->end ?? '-' }}
                    </div>
                </div>

                {{-- Petugas Section --}}
                <div class="mb-4">
                    <h4 class="font-medium text-gray-700 mb-2 border-b pb-1">👥 Petugas</h4>
                    <div class="grid grid-cols-1 gap-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pengatur Flow:</span>
                            <span class="font-medium text-right">{{ $detail->pengaturFlowOfficer?->display_name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Operator X-Ray:</span>
                            <span class="font-medium text-right">{{ $detail->operatorXrayOfficer?->display_name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pemeriksaan Manual Bagasi:</span>
                            <span class="font-medium text-right">{{ $detail->manualBagasiOfficer?->display_name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Reunited:</span>
                            <span class="font-medium text-right">{{ $detail->reunitedOfficer?->display_name ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Keterangan Section --}}
                @if($detail->keterangan)
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2 border-b pb-1">📝 Keterangan</h4>
                        <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded">{{ $detail->keterangan }}</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white p-8 shadow rounded text-center">
                <div class="text-gray-400 text-4xl mb-2">📋</div>
                <p class="text-gray-500">Tidak ada detail logbook</p>
            </div>
        @endforelse
    </div>

    {{-- Back Button --}}
    <div class="mt-6">
        <a href="{{ route('supervisor.logbook-rotasi.list') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>
</div>

<style>
    /* Custom scrollbar untuk tabel desktop */
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
@endsection
