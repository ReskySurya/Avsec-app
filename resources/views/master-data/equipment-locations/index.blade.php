@extends('layouts.app')

@section('title', 'Daftar Equipment & Location')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-700">Equipment & Location Management</h2>
        <a href="#"
           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700">
            Tambah Data
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Equipment Section --}}
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700">Daftar Equipment</h3>
            </div>
            <div class="overflow-x-auto p-4">
                @if($equipments->count())
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-100 text-gray-600 uppercase">
                            <tr>
                                <th class="px-4 py-2">Nama</th>
                                <th class="px-4 py-2">Deskripsi</th>
                                <th class="px-4 py-2">Pembuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($equipments as $equipment)
                                <tr class="border-b">
                                    <td class="px-4 py-2 font-medium">{{ $equipment->name }}</td>
                                    <td class="px-4 py-2">{{ Str::limit($equipment->description, 50) }}</td>
                                    <td class="px-4 py-2">{{ $equipment->creator->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-center text-gray-500 py-4">Belum ada data Equipment.</p>
                @endif
            </div>
        </div>

        {{-- Location Section --}}
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700">Daftar Location</h3>
            </div>
            <div class="overflow-x-auto p-4">
                @if($locations->count())
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-100 text-gray-600 uppercase">
                            <tr>
                                <th class="px-4 py-2">Nama</th>
                                <th class="px-4 py-2">Deskripsi</th>
                                <th class="px-4 py-2">Pembuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($locations as $location)
                                <tr class="border-b">
                                    <td class="px-4 py-2 font-medium">{{ $location->name }}</td>
                                    <td class="px-4 py-2">{{ Str::limit($location->description, 50) }}</td>
                                    <td class="px-4 py-2">{{ $location->creator->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-center text-gray-500 py-4">Belum ada data Location.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
