@extends('layouts.app')

@section('title', $folder->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
    <div class="container mx-auto px-3 sm:px-4 lg:px-8 py-6 sm:py-8">
        {{-- Enhanced Mobile Breadcrumbs --}}
        <div class="mb-4 sm:mb-6">
            <nav class="flex items-center space-x-2 text-sm text-gray-600">
                <a href="{{ route('pmik.index') }}" class="flex items-center hover:text-blue-600 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    <span class="hidden sm:inline">PM & IK</span>
                    <span class="sm:hidden">Beranda</span>
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="font-medium text-gray-900 truncate">{{ Str::limit($folder->name, 30) }}</span>
            </nav>
        </div>

        {{-- Enhanced Header Section --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6 sm:mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-4 sm:px-8 py-6 sm:py-8">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-3 bg-white/20 rounded-xl">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-white leading-tight">{{ $folder->name }}
                                </h1>
                                <p class="text-blue-100 text-sm sm:text-base mt-1">
                                    {{ $folder->documents->count() }} dokumen tersimpan
                                </p>
                            </div>
                        </div>
                        <p class="text-blue-50 leading-relaxed max-w-2xl">
                            {{ $folder->description ?? 'Folder ini berisi kumpulan dokumen prosedur mutu dan instruksi
                            kerja yang dapat diakses oleh tim.' }}
                        </p>
                    </div>

                    @if(auth()->user()->isSuperAdmin())
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <a href="{{ route('folders.edit', $folder) }}"
                            class="inline-flex items-center justify-center gap-2 bg-white/20 hover:bg-white/30 text-white font-semibold px-4 py-2.5 rounded-xl transition-all duration-300 backdrop-blur-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Edit Folder</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="px-4 sm:px-8 py-4 bg-gray-50 border-t border-gray-100">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-lg sm:text-xl font-bold text-gray-900">{{ $folder->documents->count() }}</p>
                        <p class="text-xs sm:text-sm text-gray-600">Total Dokumen</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg sm:text-xl font-bold text-gray-900">{{ $folder->created_at->format('M Y') }}
                        </p>
                        <p class="text-xs sm:text-sm text-gray-600">Dibuat</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg sm:text-xl font-bold text-gray-900">{{ $folder->updated_at->diffForHumans() }}
                        </p>
                        <p class="text-xs sm:text-sm text-gray-600">Update Terakhir</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg sm:text-xl font-bold text-gray-900">
                            {{ number_format($folder->documents->sum('file_size') / 1024 / 1024, 1) }} MB
                        </p>
                        <p class="text-xs sm:text-sm text-gray-600">Total Ukuran</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Enhanced Upload Form (Mobile-first) --}}
        @if(auth()->user()->isSuperAdmin())
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-6 sm:mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-4 sm:px-8 py-4">
                <h2 class="text-lg sm:text-xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload Dokumen Baru
                </h2>
                <p class="text-green-50 text-sm mt-1">Tambahkan dokumen PDF ke dalam folder ini</p>
            </div>

            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data"
                class="p-4 sm:p-8">
                @csrf
                <input type="hidden" name="folder_id" value="{{ $folder->id }}">
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                            Judul Dokumen *
                        </label>
                        <input type="text" name="title" id="title"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 text-gray-900 placeholder-gray-400"
                            placeholder="Contoh: IK Penggunaan Mesin X atau PM Proses Produksi Y" required>
                        @error('title')
                        <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">
                            File Dokumen (PDF) *
                        </label>
                        <div class="relative">
                            <input type="file" name="file" id="file" accept=".pdf"
                                class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                required>
                        </div>
                        <p class="text-gray-500 text-xs mt-2 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Hanya file PDF yang diizinkan (maks 10MB)
                        </p>
                        @error('file')
                        <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-100">
                        <button type="submit"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold px-6 py-3 rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span>Upload Dokumen</span>
                        </button>
                        <button type="reset"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Reset</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endif

        {{-- Enhanced Document List --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-4 sm:px-8 py-4 sm:py-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Daftar Dokumen
                        </h2>
                        <p class="text-gray-300 text-sm mt-1">{{ $folder->documents->count() }} dokumen tersedia</p>
                    </div>

                    {{-- Search and Filter (Mobile-friendly) --}}
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <div class="relative">
                            <input type="text" id="searchDocuments" placeholder="Cari dokumen..."
                                class="w-full sm:w-64 px-4 py-2 pl-10 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-300 focus:bg-white focus:text-gray-900 focus:placeholder-gray-500 transition-all duration-300">
                            <svg class="w-4 h-4 absolute left-3 top-3 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <select id="sortDocuments"
                            class="px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:bg-white focus:text-gray-900 transition-all duration-300">
                            <option value="newest">Terbaru</option>
                            <option value="oldest">Terlama</option>
                            <option value="name">Nama A-Z</option>
                            <option value="size">Ukuran</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="documentsList" class="divide-y divide-gray-100">
                @forelse ($folder->documents as $index => $document)
                <div class="document-item p-4 sm:p-6 hover:bg-gray-50 transition-colors"
                    data-name="{{ strtolower($document->title) }}" data-date="{{ $document->created_at->timestamp }}"
                    data-size="{{ $document->file_size }}">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        {{-- Document Icon and Info --}}
                        <div class="flex items-start gap-4 flex-1">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 text-base sm:text-lg leading-tight mb-2">
                                    {{ $document->title }}
                                </h3>
                                <div
                                    class="flex flex-wrap items-center gap-2 sm:gap-4 text-xs sm:text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        {{ number_format($document->file_size / 1024, 0) }} KB
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $document->uploader->name }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $document->created_at->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2 sm:gap-3">
                            <a href="{{ route('documents.viewer', $document) }}" {{-- <-- Arahkan ke route baru --}}
                                class="flex items-center justify-center w-10 h-10 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-xl transition-colors"
                                title="Lihat Dokumen">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>

                            <a href="{{ route('documents.download', $document) }}"
                                class="flex items-center justify-center w-10 h-10 bg-green-50 hover:bg-green-100 text-green-600 rounded-xl transition-colors"
                                title="Unduh Dokumen">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>

                            @if(auth()->user()->isSuperAdmin())
                            <div class="relative">
                                <button onclick="toggleDocumentMenu('doc-{{ $document->id }}')"
                                    class="flex items-center justify-center w-10 h-10 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-xl transition-colors"
                                    title="More Options">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </button>
                                <div id="doc-{{ $document->id }}"
                                    class="hidden absolute right-0 top-12 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-10">
                                    <form action="{{ route('documents.destroy', $document) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus Dokumen
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Mobile-only expanded view --}}
                    <div class="mt-4 pt-4 border-t border-gray-100 sm:hidden">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <a href="{{ route('documents.viewer', $document) }}" 
                                class="flex flex-col items-center gap-2 p-3 bg-blue-50 rounded-xl text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span class="text-sm font-medium">Lihat</span>
                            </a>
                            <a href="{{ route('documents.download', $document) }}"
                                class="flex flex-col items-center gap-2 p-3 bg-green-50 rounded-xl text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span class="text-sm font-medium">Unduh</span>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                {{-- Enhanced Empty State --}}
                <div class="text-center py-16 sm:py-20 px-4">
                    <div
                        class="mx-auto w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-700 mb-2">Folder Masih Kosong</h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto leading-relaxed">
                        Belum ada dokumen yang diunggah ke dalam folder ini.
                        @if(auth()->user()->isSuperAdmin())
                        Mulai upload dokumen pertama Anda.
                        @else
                        Hubungi administrator untuk menambahkan dokumen.
                        @endif
                    </p>
                    @if(auth()->user()->isSuperAdmin())
                    <div class="inline-flex items-center gap-2 text-blue-600 bg-blue-50 px-4 py-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 11l5-5m0 0l5 5m-5-5v12" />
                        </svg>
                        <span class="text-sm font-medium">Gunakan form upload di atas untuk menambah dokumen</span>
                    </div>
                    @endif
                </div>
                @endforelse
            </div>

            {{-- No Search Results --}}
            <div id="noResults" class="hidden text-center py-12 px-4">
                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Dokumen Tidak Ditemukan</h3>
                <p class="text-gray-500">Coba gunakan kata kunci yang berbeda atau periksa ejaan Anda.</p>
            </div>
        </div>

        {{-- Back to Folders Button --}}
        <div class="mt-8 text-center">
            <a href="{{ route('pmik.index') }}"
                class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 font-semibold px-6 py-3 rounded-xl shadow-md border border-gray-200 transition-all duration-300 hover:shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                </svg>
                <span>Kembali ke Daftar Folder</span>
            </a>
        </div>
    </div>
</div>

{{-- JavaScript for Enhanced Functionality --}}
<script>
    // Toggle document menu
function toggleDocumentMenu(id) {
    const menu = document.getElementById(id);
    const isHidden = menu.classList.contains('hidden');

    // Close all menus
    document.querySelectorAll('[id^="doc-"]').forEach(el => {
        el.classList.add('hidden');
    });

    // Toggle current menu
    if (isHidden) {
        menu.classList.remove('hidden');
    }
}

// Close menus when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="toggleDocumentMenu"]') && !event.target.closest('[id^="doc-"]')) {
        document.querySelectorAll('[id^="doc-"]').forEach(el => {
            el.classList.add('hidden');
        });
    }
});

// Search and Sort Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchDocuments');
    const sortSelect = document.getElementById('sortDocuments');
    const documentsList = document.getElementById('documentsList');
    const noResults = document.getElementById('noResults');

    function filterAndSort() {
        const searchTerm = searchInput.value.toLowerCase();
        const sortBy = sortSelect.value;
        const items = Array.from(document.querySelectorAll('.document-item'));

        // Filter items
        let visibleItems = items.filter(item => {
            const name = item.getAttribute('data-name');
            const isVisible = name.includes(searchTerm);
            item.style.display = isVisible ? 'block' : 'none';
            return isVisible;
        });

        // Sort items
        visibleItems.sort((a, b) => {
            switch(sortBy) {
                case 'oldest':
                    return parseInt(a.getAttribute('data-date')) - parseInt(b.getAttribute('data-date'));
                case 'newest':
                    return parseInt(b.getAttribute('data-date')) - parseInt(a.getAttribute('data-date'));
                case 'name':
                    return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
                case 'size':
                    return parseInt(b.getAttribute('data-size')) - parseInt(a.getAttribute('data-size'));
                default:
                    return 0;
            }
        });

        // Reorder items in DOM
        visibleItems.forEach(item => {
            documentsList.appendChild(item);
        });

        // Show/hide no results message
        if (visibleItems.length === 0 && items.length > 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }

    if (searchInput && sortSelect) {
        searchInput.addEventListener('input', filterAndSort);
        sortSelect.addEventListener('change', filterAndSort);
    }
});

// File upload preview and validation
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const maxSize = 10 * 1024 * 1024; // 10MB
                if (file.size > maxSize) {
                    alert('File terlalu besar! Maksimal 10MB.');
                    e.target.value = '';
                    return;
                }

                if (file.type !== 'application/pdf') {
                    alert('Hanya file PDF yang diizinkan!');
                    e.target.value = '';
                    return;
                }
            }
        });
    }
});
</script>
@endsection
