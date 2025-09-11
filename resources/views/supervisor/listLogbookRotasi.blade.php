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
        <div >
            <div class="bg-white rounded-xl p-3 sm:p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <div class="flex items-center">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Filter Data</h3>
                        @if(request()->has('status') && request()->get('status') !== '')
                        <a href="{{ request()->url() }}"
                            class="ml-3 text-xs sm:text-sm text-blue-600 hover:text-blue-800 flex items-center">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Clear Filter
                        </a>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                        <div class="flex items-center">
                            <label for="statusFilter" class="text-xs sm:text-sm text-gray-600 mr-2">Status:</label>
                            <select id="statusFilter"
                                class="text-xs sm:text-sm border border-gray-300 rounded-lg px-2 sm:px-3 py-1.5 sm:py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                onchange="applyFilter(this.value)">
                                <option value="">Semua Status</option>
                                <option value="draft" {{ request()->get('status') === 'draft' ? 'selected' : '' }}>Draft
                                </option>
                                <option value="submitted" {{ request()->get('status') === 'submitted' ? 'selected' : ''
                                }}>Menunggu Persetujuan</option>
                                <option value="approved" {{ request()->get('status') === 'approved' ? 'selected' : ''
                                }}>Selesai</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Active Filter Tags --}}
                @if(request()->has('status') && request()->get('status') !== '')
                <div class="flex flex-wrap gap-2">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Status: {{ ucfirst(request()->get('status')) }}
                        <a href="{{ request()->url() }}" class="ml-1.5 inline-flex items-center">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </a>
                    </span>
                </div>
                @endif
            </div>
        </div>


        <div class="p-4 sm:p-4">
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
<script>
    function applyFilter(status) {
        const url = new URL(window.location);
        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        url.searchParams.delete('page'); // Reset pagination when filtering
        window.location.href = url.toString();
    }
</script>
@endsection