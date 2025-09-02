@extends('layouts.app')

@section('title', 'Buat Folder Baru')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 max-w-2xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-6">
            <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Buat Folder Baru</h1>
        </div>

        {{-- Form --}}
        <form action="{{ route('folders.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                {{-- Nama Folder --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Folder</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
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
                              placeholder="Jelaskan isi dari folder ini...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t">
                <a href="{{ route('pmik.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Folder</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
