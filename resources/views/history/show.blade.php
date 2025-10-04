@extends('layouts.app')

@section('title', 'History for ' . ucfirst($category))

@push('styles')
<style>
    body {
        background-color: #f8fafc;
        /* Light gray background */
    }
</style>
@endpush

@section('content')
<div class="mx-auto p-0 sm:p-6 min-h-screen pt-5 sm:pt-20">
    <div class="flex items-center justify-between mb-8">
        <a href="{{ route('history.index') }}"
            class="inline-flex items-center text-gray-600 hover:text-indigo-600 transition-colors duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                    clip-rule="evenodd" />
            </svg>
            <span>Kembali ke Kategori</span>
        </a>
        <h3 class="text-lg sm:text-xl font-bold text-gray-800 leading-tight text-right">Riwayat: {{
            ucwords(str_replace('-', ' ', $category)) }}</h3>
    </div>

    {{-- Filter Form --}}
    <div class="bg-white shadow-lg rounded-xl p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Filter History</h2>
        <form action="{{ route('history.show', ['category' => $category]) }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="form_type" class="block text-sm font-medium text-gray-700 mb-1">Jenis Formulir</label>
                    <select name="form_type" id="form_type"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua</option>
                        @foreach($form_types as $type)
                        <option value="{{ $type }}" {{ request('form_type')==$type ? 'selected' : '' }}>{{
                            ucwords(str_replace('_', ' ', $type)) }}</option>
                        @endforeach
                    </select>
                </div>

                @if($locations->isNotEmpty())
                <div>
                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <select name="location_id" id="location_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $id => $name)
                        <option value="{{ $id }}" {{ request('location_id')==$id ? 'selected' : '' }}>{{ $name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if($isShiftFilterable)
                <div>
                    <label for="shift" class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                    <select name="shift" id="shift"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Semua</option>
                        <option value="pagi" {{ request('shift')=='pagi' ? 'selected' : '' }}>Pagi</option>
                        <option value="siang" {{ request('shift')=='siang' ? 'selected' : '' }}>Siang</option>
                        <option value="malam" {{ request('shift')=='malam' ? 'selected' : '' }}>Malam</option>
                    </select>
                </div>
                @endif
            </div>
            <div class="mt-6 text-right">
                <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- History Data --}}
    <div class="space-y-8">
        @forelse ($data as $key => $items)
        @php
        $isGrouped = !is_numeric($key);
        $groupItems = $isGrouped ? $items : collect([$data]);
        if (!$isGrouped) {
        $groupItems = $data;
        }

        $groupTitle = $isGrouped ? ucwords(str_replace('_', ' ', $key)) : (request('form_type') ?
        ucwords(str_replace('_', ' ', request('form_type'))) : 'Hasil Pencarian');
        @endphp

        <div class="bg-white shadow-lg rounded-xl">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">{{ $groupTitle }}</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($groupItems as $item)
                <div
                    class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex-grow mb-4 sm:mb-0">
                        <div class="flex items-center mb-2 flex-wrap">
                            <span class="font-bold text-indigo-600 text-md">{{ ucwords(str_replace('_', ' ',
                                $item->form_type)) }}</span>
                            <span class="mx-2 text-gray-300">|</span>
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ ucwords($item->status == 2 ? 'approved' : ($item->status ?? 'approved')) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium text-gray-800">Petugas:</span> {{ $item->officer }}
                        </p>
                        <div class="text-sm text-gray-600 mt-1 flex items-center flex-wrap">
                            <span><span class="font-medium text-gray-800">Tanggal:</span> {{
                                \Carbon\Carbon::parse($item->date)->isoFormat('D MMMM YYYY') }}</span>
                            <span class="mx-2 text-gray-300 sm:inline">|</span>
                            <span><span class="font-medium text-gray-800">Shift:</span> {{ ucwords($item->shift ??
                                'N/A') }}</span>
                            @php
                            $location = $item->location ?? $item->location_name ?? null;
                            @endphp
                            @if($location)
                            <span class="mx-2 text-gray-300 sm:inline">|</span>
                            <span><span class="font-medium text-gray-800">Lokasi:</span> {{ ucwords($location) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-shrink-0 self-end sm:self-center">
                        <a href="{{ route('history.preview', ['category' => $category, 'id' => $item->id, 'form_type' => $item->form_type]) }}"
                            target="_blank"
                            class="inline-flex items-center p-2 bg-indigo-100 text-indigo-600 rounded-full hover:bg-indigo-200 transition-colors duration-300"
                            title="Lihat Pratinjau">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-sm text-gray-500">
                    Tidak ada data untuk grup ini.
                </div>
                @endforelse
            </div>
        </div>
        @if(!$isGrouped) @break @endif
        @empty
        <div class="bg-white shadow-lg rounded-xl p-8 text-center">
            <div class="flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-700">Data Tidak Ditemukan</h3>
                <p class="mt-1 text-sm">Tidak ada data yang disetujui untuk filter yang dipilih.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
