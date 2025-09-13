@extends('layouts.app')

@section('title', 'Edit Folder')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:mt-20">
    {{-- Breadcrumbs --}}
    <div class="text-sm breadcrumbs mb-6">
        <ul>
            <li><a href="{{ route('pmik.index') }}">PM & IK</a></li>
            <li><a href="{{ route('folders.show', $folder) }}">{{ $folder->name }}</a></li>
            <li>Edit</li>
        </ul>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 max-w-2xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-6">
            <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Folder</h1>
        </div>

        {{-- Form --}}
        <form action="{{ route('folders.update', $folder) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                {{-- Nama Folder --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Folder</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $folder->name) }}"
                           class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Contoh: Prosedur Mutu 2025" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi Folder --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                    <textarea name="description" id="description" rows="4"
                              class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Jelaskan isi dari folder ini...">{{ old('description', $folder->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t">
                <a href="{{ route('folders.show', $folder) }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
