@extends('layouts.app')

@section('title', 'PM dan IK')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Dokumen PM & IK</h1>
        @if(auth()->user()->isSuperAdmin())
            <a href="{{ route('folders.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Buat Folder Baru</span>
            </a>
        @endif
    </div>

    {{-- Grid Folder --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($folders as $folder)
            <div class="bg-white rounded-xl shadow-md p-6 flex flex-col group transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                {{-- Ikon Folder Besar --}}
                <div class="flex items-center justify-between mb-4">
                     <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                     {{-- Tombol Aksi SuperAdmin --}}
                     @if(auth()->user()->isSuperAdmin())
                         <div class="flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                             <a href="{{ route('folders.edit', $folder) }}" class="p-2 text-gray-500 hover:bg-gray-200 hover:text-blue-600 rounded-full">
                                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                             </a>
                             <form action="{{ route('folders.destroy', $folder) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus folder ini beserta semua isinya?')">
                                 @csrf
                                 @method('DELETE')
                                 <button type="submit" class="p-2 text-gray-500 hover:bg-gray-200 hover:text-red-600 rounded-full">
                                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                 </button>
                             </form>
                         </div>
                     @endif
                </div>

                {{-- Konten Utama Kartu --}}
                <div class="flex flex-col flex-grow">
                    <h3 class="text-lg font-bold text-gray-900 truncate">{{ $folder->name }}</h3>
                    <p class="text-gray-600 text-sm mt-1 mb-4 h-10 line-clamp-2">{{ $folder->description ?? 'Tidak ada deskripsi.' }}</p>

                    {{-- Metadata --}}
                    <div class="mt-auto border-t border-gray-100 pt-4 space-y-2 text-sm text-gray-500">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>{{ $folder->documents_count ?? $folder->documents->count() }} dokumen</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Dibuat: {{ $folder->created_at->format('d M Y') }}</span>
                        </div>
                    </div>

                    {{-- Tombol Aksi Utama --}}
                    <a href="{{ route('folders.show', $folder) }}"
                       class="block w-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold text-center py-2.5 rounded-lg mt-5 transition-colors">
                        Buka Folder
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center text-center py-20 bg-gray-50 rounded-lg">
                <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                <h3 class="mt-4 text-xl font-semibold text-gray-700">Belum Ada Folder</h3>
                <p class="mt-2 text-gray-500">Saat ini belum ada folder dokumen yang tersedia.</p>
                @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('folders.create') }}" class="mt-6 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <span>Buat Folder Pertama Anda</span>
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Paginasi --}}
    <div class="mt-8">
        {{ $folders->links() }}
    </div>
</div>
@endsection
