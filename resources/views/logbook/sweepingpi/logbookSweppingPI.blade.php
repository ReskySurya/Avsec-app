@extends('layouts.app')

@section('title', 'Logbook Sweeping Prohibited Items')

@section('content')
<div x-data="{}" class="container mx-auto p-4 max-w-full mt-20">

    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 rounded-3xl shadow-xl p-8 mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 text-white">
            <div class="flex items-center space-x-4">
                <div>
                    <h1 class="text-3xl font-bold">Daftar Tenant</h1>
                    <p class="text-blue-100 text-sm mt-1">Pilih tenant untuk melihat atau mengelola logbook sweeping.</p>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-white/20 rounded-xl p-4 backdrop-blur-sm border border-white/30 w-full lg:w-auto mt-4 lg:mt-0">
                <div class="text-center">
                    <div class="text-2xl font-bold text-white">{{ count($tenantList ?? []) }}</div>
                    <div class="text-xs text-blue-100 uppercase tracking-wide">Total Tenant</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="mt-2">
        <!-- Mobile Card View -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:hidden">
            @forelse($tenantList ?? [] as $tenant)
            <div class="group bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 overflow-hidden cursor-pointer"
                 @click="window.location.href='{{ route('logbookSweppingPI.detail.index',$tenant->tenantID) }}'">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                            {{ $tenant->tenant_name }}
                        </h4>
                        <div class="bg-blue-50 text-blue-600 text-xs font-medium px-3 py-1 rounded-full">
                            ID: {{ $tenant->tenantID }}
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">Klik untuk melihat logbook sweeping.</p>
                </div>
            </div>
            @empty
            <div class="col-span-1 sm:col-span-2 text-center py-16">
                <div class="bg-white rounded-2xl p-8 shadow-md border border-gray-200">
                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada data tenant</h3>
                    <p class="text-gray-500 text-sm">Data tenant tidak ditemukan.</p>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-blue-50 border-b-2 border-blue-100">
                            <tr>
                                <th class="px-8 py-4 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">No</th>
                                <th class="px-8 py-4 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">ID Tenant</th>
                                <th class="px-8 py-4 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Nama Tenant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($tenantList ?? [] as $tenant)
                            <tr class="group hover:bg-blue-50/60 transition-all duration-200 cursor-pointer"
                                @click="window.location.href='{{ route('logbookSweppingPI.detail.index',$tenant->tenantID) }}'">
                                <td class="px-8 py-5">
                                    <div class="text-gray-600 text-sm font-medium">{{ $loop->iteration }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-gray-800 font-semibold group-hover:text-blue-700 transition-colors">
                                        {{ $tenant->tenantID }}
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-gray-700 group-hover:text-blue-700 transition-colors">
                                        {{ $tenant->tenant_name }}
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-8 py-16 text-center">
                                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada data tenant</h3>
                                    <p class="text-gray-500 text-sm">Data tenant tidak ditemukan.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
