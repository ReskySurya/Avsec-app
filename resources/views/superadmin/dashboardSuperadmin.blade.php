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

    @include('superadmin.partials.user-info')

    @include('superadmin.partials.dailytest-stats')
    @include('superadmin.partials.logbook-stats')
    @include('superadmin.partials.checklist-stats')

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
<style>
    .gauge-container {
        display: inline-block;
        text-align: center;
        width: 160px;
    }

    .gauge {
        position: relative;
        width: 140px;
        height: 70px;
        margin: 0 auto 10px;
    }

    .gauge-background {
        width: 140px;
        height: 70px;
        background: #e0e0e0;
        border-radius: 70px 70px 0 0;
        position: relative;
        overflow: hidden;
    }

    .gauge-fill {
        position: absolute;
        top: 0;
        left: 0;
        width: 140px;
        height: 70px;
        background: conic-gradient(from 180deg, #4ade80 0deg, #4ade80 var(--percentage), transparent var(--percentage));
        border-radius: 70px 70px 0 0;
        transition: all 0.8s ease-in-out;
    }

    .gauge-center {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 120px;
        height: 60px;
        background: white;
        border-radius: 60px 60px 0 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 2;
    }

    .gauge-percentage {
        font-size: 28px;
        font-weight: bold;
        color: #333;
        line-height: 1;
    }

    .gauge-label {
        font-size: 11px;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .gauge-scale {
        bottom: -5px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: #999;
    }
</style>
@endsection