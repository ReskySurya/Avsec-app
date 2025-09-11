@extends('layouts.app')

@section('title', 'Logbook Rotasi (Supervisor)')
@section('content')
@php
    $typeForm = request('type', 'pscp'); // Default to pscp
@endphp
<div class="mx-auto p-0 sm:p-6 min-h-screen pt-5 lg:pt-20">
    <div class="bg-white shadow-xl rounded-none sm:rounded-2xl overflow-hidden border-0 sm:border border-gray-100">
        <div class="bg-gradient-to-r from-blue-500 to-teal-600 px-4 sm:px-6 py-4 sm:py-6 text-white">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold mb-1">Logbook Rotasi</h3>
                    <p class="text-blue-100 text-sm sm:text-base">Daftar logbook rotasi yang telah dibuat oleh officer.</p>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-6">
            <!-- Tab Navigation -->
            <div class="mb-4 border-b border-gray-200">
                <div class="flex space-x-1 sm:space-x-2 overflow-x-auto">
                    <a href="{{ route('supervisor.logbook-rotasi.list', ['type' => 'pscp']) }}"
                        class="flex-shrink-0 px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium rounded-t-lg transition-colors duration-200 whitespace-nowrap flex items-center
                              {{ $typeForm === 'pscp' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i class="fas fa-shield-alt mr-2"></i>Rotasi PSCP
                    </a>
                    <a href="{{ route('supervisor.logbook-rotasi.list', ['type' => 'hbscp']) }}"
                        class="flex-shrink-0 px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium rounded-t-lg transition-colors duration-200 whitespace-nowrap flex items-center
                              {{ $typeForm === 'hbscp' ? 'bg-teal-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i class="fas fa-suitcase-rolling mr-2"></i>Rotasi HBSCP
                    </a>
                </div>
            </div>

            @if ($typeForm === 'pscp')
                {{-- Desktop Table for PSCP --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Logbook</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($logbooksPSCP as $logbook)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $logbook->id }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($logbook->date)->isoFormat('dddd, D MMMM Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     @if($logbook->status === 'submitted')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Menunggu Approval</span>
                                    @elseif($logbook->status === 'approved')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Disetujui</span>
                                    @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">Draft</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $logbook->creator->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('supervisor.logbook-rotasi.detail', $logbook->id) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-12">
                                    <p class="text-gray-500">Tidak ada data logbook PSCP.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Card View for PSCP --}}
                <div class="lg:hidden space-y-4">
                    @forelse($logbooksPSCP as $logbook)
                    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    ID: {{ $logbook->id }}
                                </span>
                                <p class="text-sm text-gray-500 mt-2">Oleh: {{ $logbook->creator->name ?? 'N/A' }}</p>
                            </div>
                            @if($logbook->status === 'submitted')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu</span>
                            @elseif($logbook->status === 'approved')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Draft</span>
                            @endif
                        </div>
                        <div class="mt-4">
                            <p class="text-sm font-semibold text-gray-800">{{ \Carbon\Carbon::parse($logbook->date)->isoFormat('dddd, D MMMM Y') }}</p>
                        </div>
                        <div class="mt-4 border-t pt-4">
                            <a href="{{ route('supervisor.logbook-rotasi.detail', $logbook->id) }}" class="block w-full text-center text-sm font-medium text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-lg">Lihat Detail</a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <p class="text-gray-500">Tidak ada data logbook PSCP.</p>
                    </div>
                    @endforelse
                </div>

                @if(method_exists($logbooksPSCP, 'links'))
                <div class="mt-4">
                    {{ $logbooksPSCP->appends(['type' => 'pscp'])->links() }}
                </div>
                @endif
            @else
                {{-- Desktop Table for HBSCP --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Logbook</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($logbooksHBSCP as $logbook)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-teal-100 text-teal-800">
                                        {{ $logbook->id }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($logbook->date)->isoFormat('dddd, D MMMM Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     @if($logbook->status === 'submitted')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Menunggu Approval</span>
                                    @elseif($logbook->status === 'approved')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Disetujui</span>
                                    @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">Draft</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $logbook->creator->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('supervisor.logbook-rotasi.detail', $logbook->id) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-12">
                                    <p class="text-gray-500">Tidak ada data logbook HBSCP.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Card View for HBSCP --}}
                <div class="lg:hidden space-y-4">
                    @forelse($logbooksHBSCP as $logbook)
                    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-800">
                                    ID: {{ $logbook->id }}
                                </span>
                                <p class="text-sm text-gray-500 mt-2">Oleh: {{ $logbook->creator->name ?? 'N/A' }}</p>
                            </div>
                            @if($logbook->status === 'submitted')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu</span>
                            @elseif($logbook->status === 'approved')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Draft</span>
                            @endif
                        </div>
                        <div class="mt-4">
                            <p class="text-sm font-semibold text-gray-800">{{ \Carbon\Carbon::parse($logbook->date)->isoFormat('dddd, D MMMM Y') }}</p>
                        </div>
                        <div class="mt-4 border-t pt-4">
                            <a href="{{ route('supervisor.logbook-rotasi.detail', $logbook->id) }}" class="block w-full text-center text-sm font-medium text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-lg">Lihat Detail</a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <p class="text-gray-500">Tidak ada data logbook HBSCP.</p>
                    </div>
                    @endforelse
                </div>

                @if(method_exists($logbooksHBSCP, 'links'))
                <div class="mt-4">
                    {{ $logbooksHBSCP->appends(['type' => 'hbscp'])->links() }}
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
