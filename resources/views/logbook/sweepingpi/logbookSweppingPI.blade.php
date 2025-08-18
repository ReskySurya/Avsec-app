@extends('layouts.app')

@section('title', 'Logbook Sweeping Prohibited Items')

@section('content')
<div x-data="{}" class="container mx-auto p-4 max-w-full overflow-x-auto mt-20">

    <!-- Header -->
    <div class="bg-gradient-to-br from-slate-100 to-gray-300 rounded-3xl shadow-lg border border-gray-200/50 p-8 mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="flex items-center space-x-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 mb-1">Daftar Tenant</h1>
                    <p class="text-slate-500 text-sm">Kelola data tenant dengan mudah</p>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200/70">
                <div class="text-center">
                    <div class="text-2xl font-bold text-slate-700">{{ count($tenantList ?? []) }}</div>
                    <div class="text-xs text-slate-500 uppercase tracking-wide">Total Tenant</div>
                </div>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8 md:hidden">
            @forelse($tenantList ?? [] as $index => $tenant)
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200/70 overflow-hidden hover:border-gray-300/70">
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-slate-800 group-hover:text-slate-900 transition-colors">
                                {{ $tenant->tenantID }}
                            </h4>
                            <div class="bg-slate-100 text-slate-600 text-xs font-medium px-3 py-1 rounded-full">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                        <p class="text-slate-600 text-sm leading-relaxed">{{ $tenant->tenant_name }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-1 sm:col-span-2 text-center py-16">
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200/70">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-slate-700 mb-2">Belum ada data tenant</h3>
                    <p class="text-slate-500 text-sm">Tambahkan tenant pertama untuk memulai</p>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="mt-8 hidden md:block">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-400/70 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-200 border-b border-gray-400">
                                <th class="px-8 py-5 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">No</th>
                                <th class="px-8 py-5 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">ID Tenant</th>
                                <th class="px-8 py-5 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Nama Tenant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($tenantList ?? [] as $index => $tenant)
                            <tr class="group hover:bg-slate-50/50 transition-all duration-200 cursor-pointer"
                                @click="window.location.href='{{ route('logbookSweppingPI.detail.index',$tenant->tenantID) }}'">
                                <td class="px-8 py-6">
                                    <div class=" text-slate-600 text-sm font-medium px-3 py-1 inline-block">
                                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-slate-800 font-medium group-hover:text-slate-900 transition-colors">
                                        {{ $tenant->tenantID }}
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-slate-700 group-hover:text-slate-800 transition-colors">
                                        {{ $tenant->tenant_name }}
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-16 text-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-slate-700 mb-2">Belum ada data tenant</h3>
                                    <p class="text-slate-500">Tambahkan tenant pertama untuk memulai</p>
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