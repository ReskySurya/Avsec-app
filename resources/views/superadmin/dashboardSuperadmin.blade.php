@extends('layouts.app')

@section('title', 'Superadmin Dashboard')

@section('content')
<div x-data="{
    isModalOpen: false,
    modalTitle: '',
    modalData: {},
    modalBreakdownTitle: 'Lokasi'
}" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:mt-20">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Superadmin Dashboard
        </h1>
        <p class="text-gray-600 mt-1">
            Selamat datang, {{ Auth::user()->name }}!
        </p>
    </div>

    @include('partials.user-info')
    @include('partials.dailytest-stats')
    @include('partials.logbook-stats')
    @include('partials.checklist-stats')

    <!-- Modal -->
    <div x-show="isModalOpen" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="isModalOpen = false" class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-cyan-600 text-white p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold" x-text="`Detail untuk ${modalTitle}`"></h2>
                    <button @click="isModalOpen = false" class="text-blue-100 hover:text-white text-3xl leading-none">&times;</button>
                </div>
                <p class="text-blue-100">Rincian jumlah form per <span x-text="modalBreakdownTitle.toLowerCase()"></span> untuk hari ini.</p>
            </div>
            <div class="p-6 max-h-[60vh] overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" x-text="modalBreakdownTitle"></th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Form Disetujui</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Form</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(data, key) in modalData" :key="key">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="key"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="data.approved"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="data.total"></td>
                            </tr>
                        </template>
                        <template x-if="Object.keys(modalData).length === 0">
                            <tr>
                                <td colspan="3" class="text-center py-8 text-gray-500">Tidak ada data rincian untuk ditampilkan.</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
