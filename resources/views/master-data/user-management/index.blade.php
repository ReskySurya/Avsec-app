@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class=" mx-auto sm:mt-6 lg:mt-20 px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">User Management</h1>
        <p class="text-gray-600">Kelola petugas dan supervisor dalam sistem</p>
    </div>

    <!-- Tab Navigation -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-1">
            <nav class="flex space-x-1" aria-label="Tabs" id="userTabs">
                <button
                    class="tab-button active flex-1 sm:flex-none bg-blue-600 text-white px-4 py-2.5 rounded-md font-medium text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-all duration-200"
                    data-target="officers">
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="hidden sm:inline">Petugas</span>
                        <span class="sm:hidden">Petugas</span>
                    </span>
                </button>
                <button
                    class="tab-button flex-1 sm:flex-none bg-transparent hover:bg-gray-100 text-gray-600 hover:text-gray-800 px-4 py-2.5 rounded-md font-medium text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-1 transition-all duration-200"
                    data-target="supervisors">
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="hidden sm:inline">Supervisor</span>
                        <span class="sm:hidden">Supervisor</span>
                    </span>
                </button>
                <button
                    class="tab-button flex-1 sm:flex-none bg-transparent hover:bg-gray-100 text-gray-600 hover:text-gray-800 px-3 py-2.5 rounded-md font-medium text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-1 transition-all duration-200"
                    data-target="superadmins">
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="hidden sm:inline">Superadmin</span>
                        <span class="sm:hidden">Admin</span>
                    </span>
                </button>
            </nav>
        </div>
    </div>

    <!-- Officers Tab Content -->
    <div id="officers" class="tab-content">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header with Add Button -->
            <div
                class="px-4 sm:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-medium text-gray-900 mb-3 sm:mb-0">Daftar Petugas</h3>
                <button
                    class="btn-tambah bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Petugas
                </button>
            </div>

            <!-- Search and Filter -->
            <div class="px-4 sm:px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" placeholder="Cari petugas..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <select
                        class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option>Semua Status</option>
                        <option>Aktif</option>
                        <option>Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <!-- Table - Desktop -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                NIP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lisensi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($officers as $officer)
                        <tr class="hover:bg-gray-50" data-user-id="{{ $officer->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <img class="h-10 w-10 rounded-full object-cover"
                                            src="https://ui-avatars.com/api/?name={{ urlencode($officer->name) }}&background=3b82f6&color=fff"
                                            alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 data-name">{{ $officer->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 data-nip">{{ $officer->nip }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 data-lisensi">{{
                                $officer->lisensi }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 data-email">{{ $officer->email
                                }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button class="btn-edit text-blue-600 hover:text-blue-900">Edit</button>
                                    <button class="btn-hapus text-red-600 hover:text-red-900">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Cards - Mobile -->
            <div class="md:hidden">
                <div class="bg-white divide-y divide-gray-200">
                    @foreach($officers as $officer)
                    <div class="p-4 hover:bg-gray-50" data-user-id="{{ $officer->id }}">
                        <div class="flex items-center space-x-4">
                            <img class="h-12 w-12 rounded-full object-cover"
                                src="https://ui-avatars.com/api/?name={{ urlencode($officer->name) }}&background=3b82f6&color=fff"
                                alt="">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900 truncate data-name">{{ $officer->name }}
                                    </p>
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                </div>
                                <p class="text-sm text-gray-500 truncate data-email">{{ $officer->email }}</p>
                                <p class="text-xs text-gray-400">NIP: <span class="data-nip">{{ $officer->nip }}</span>
                                    • Lisensi: <span class="data-lisensi">
                                        {{ $officer->lisensi }}</span></p>
                                <div class="mt-2 flex space-x-3">
                                    <button class="btn-edit text-blue-600 hover:text-blue-900 text-sm">Edit</button>
                                    <button class="btn-hapus text-red-600 hover:text-red-900 text-sm">Hapus</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            <div class="px-4 sm:px-6 py-3 bg-white border-t border-gray-200 flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Sebelumnya</button>
                    <button
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Selanjutnya</button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">Menampilkan <span class="font-medium">1</span> sampai <span
                                class="font-medium">{{ count($officers) }}</span> dari <span class="font-medium">{{
                                count($officers) }}</span> hasil</p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <button
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Sebelumnya</button>
                            <button
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-50 text-sm font-medium text-blue-600">1</button>
                            <button
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Selanjutnya</button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supervisors Tab Content -->
    <div id="supervisors" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header with Add Button -->
            <div
                class="px-4 sm:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-medium text-gray-900 mb-3 sm:mb-0">Daftar Supervisor</h3>
                <button
                    class="btn-tambah bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Supervisor
                </button>
            </div>

            <!-- Search and Filter -->
            <div class="px-4 sm:px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" placeholder="Cari Supervisor..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <select
                        class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option>Semua Status</option>
                        <option>Aktif</option>
                        <option>Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <!-- Table - Desktop -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                NIP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lisensi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($supervisors as $supervisor)
                        <tr class="hover:bg-gray-50" data-user-id="{{ $supervisor->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <img class="h-10 w-10 rounded-full object-cover"
                                            src="https://ui-avatars.com/api/?name={{ urlencode($supervisor->name) }}&background=3b82f6&color=fff"
                                            alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 data-name">{{ $supervisor->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 data-nip">{{ $supervisor->nip
                                }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 data-lisensi">{{
                                $supervisor->lisensi }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 data-email">{{
                                $supervisor->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button class="btn-edit text-blue-600 hover:text-blue-900">Edit</button>
                                    <button class="btn-hapus text-red-600 hover:text-red-900">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Cards - Mobile -->
            <div class="md:hidden">
                <div class="bg-white divide-y divide-gray-200">
                    @foreach($supervisors as $supervisor)
                    <div class="p-4 hover:bg-gray-50" data-user-id="{{ $supervisor->id }}">
                        <div class="flex items-center space-x-4">
                            <img class="h-12 w-12 rounded-full object-cover"
                                src="https://ui-avatars.com/api/?name={{ urlencode($supervisor->name) }}&background=3b82f6&color=fff"
                                alt="">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900 truncate data-name">{{ $supervisor->name
                                        }}</p>
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                </div>
                                <p class="text-sm text-gray-500 truncate data-email">{{ $supervisor->email }}</p>
                                <p class="text-xs text-gray-400">NIP: <span class="data-nip">{{ $supervisor->nip
                                        }}</span> • Lisensi: <span class="data-lisensi">
                                        {{ $supervisor->lisensi }}</span></p>
                                <div class="mt-2 flex space-x-3">
                                    <button class="btn-edit text-blue-600 hover:text-blue-900 text-sm">Edit</button>
                                    <button class="btn-hapus text-red-600 hover:text-red-900 text-sm">Hapus</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            <div class="px-4 sm:px-6 py-3 bg-white border-t border-gray-200 flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Sebelumnya</button>
                    <button
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Selanjutnya</button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">Menampilkan <span class="font-medium">1</span> sampai <span
                                class="font-medium">{{ count($supervisors) }}</span> dari <span class="font-medium">{{
                                count($supervisors) }}</span> hasil</p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <button
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Sebelumnya</button>
                            <button
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-50 text-sm font-medium text-blue-600">1</button>
                            <button
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Selanjutnya</button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Superadmins Tab Content -->
    <div id="superadmins" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header with Add Button -->
            <div
                class="px-4 sm:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-medium text-gray-900 mb-3 sm:mb-0">Daftar Superadmin</h3>
                <button
                    class="btn-tambah bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Superadmin
                </button>
            </div>

            <!-- Search and Filter -->
            <div class="px-4 sm:px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" placeholder="Cari Superadmin..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <select
                        class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option>Semua Status</option>
                        <option>Aktif</option>
                        <option>Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <!-- Table - Desktop -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                NIP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lisensi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($superadmins as $superadmin)
                        <tr class="hover:bg-gray-50" data-user-id="{{ $superadmin->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <img class="h-10 w-10 rounded-full object-cover"
                                            src="https://ui-avatars.com/api/?name={{ urlencode($superadmin->name) }}&background=3b82f6&color=fff"
                                            alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 data-name">{{ $superadmin->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 data-nip">{{ $superadmin->nip
                                }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 data-lisensi">{{
                                $superadmin->lisensi }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 data-email">{{
                                $superadmin->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button class="btn-edit text-blue-600 hover:text-blue-900">Edit</button>
                                    <button class="btn-hapus text-red-600 hover:text-red-900">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Cards - Mobile -->
            <div class="md:hidden">
                <div class="bg-white divide-y divide-gray-200">
                    @foreach($superadmins as $superadmin)
                    <div class="p-4 hover:bg-gray-50" data-user-id="{{ $superadmin->id }}">
                        <div class="flex items-center space-x-4">
                            <img class="h-12 w-12 rounded-full object-cover"
                                src="https://ui-avatars.com/api/?name={{ urlencode($superadmin->name) }}&background=3b82f6&color=fff"
                                alt="">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900 truncate data-name">{{ $superadmin->name
                                        }}</p>
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                </div>
                                <p class="text-sm text-gray-500 truncate data-email">{{ $superadmin->email }}</p>
                                <p class="text-xs text-gray-400">NIP: <span class="data-nip">{{ $superadmin->nip
                                        }}</span> • Lisensi: <span class="data-lisensi">
                                        {{ $superadmin->lisensi }}</span></p>
                                <div class="mt-2 flex space-x-3">
                                    <button class="btn-edit text-blue-600 hover:text-blue-900 text-sm">Edit</button>
                                    <button class="btn-hapus text-red-600 hover:text-red-900 text-sm">Hapus</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            <div class="px-4 sm:px-6 py-3 bg-white border-t border-gray-200 flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Sebelumnya</button>
                    <button
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Selanjutnya</button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">Menampilkan <span class="font-medium">1</span> sampai <span
                                class="font-medium">{{ count($superadmins) }}</span> dari <span class="font-medium">{{
                                count($superadmins) }}</span> hasil</p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <button
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Sebelumnya</button>
                            <button
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-50 text-sm font-medium text-blue-600">1</button>
                            <button
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Selanjutnya</button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalTambah" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal panel -->
        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="formTambah" action="{{ route('users-management.store') }}" method="POST">
                @csrf

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tambah User Baru</h3>
                        <p class="text-sm text-gray-500">Isi informasi user dengan lengkap</p>
                    </div>

                    <div class="space-y-4">
                        <!-- Role -->
                        <div>
                            <label for="role_id" class="block text-sm font-medium text-gray-700">Role</label>
                            <select name="role_id" id="role_id"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                                <option value="">Pilih Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Nama -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="name" id="name"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                        </div>

                        <!-- NIP -->
                        <div>
                            <label for="nip" class="block text-sm font-medium text-gray-700">NIP</label>
                            <input type="text" name="nip" id="nip"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                        </div>

                        <!-- Lisensi -->
                        <div>
                            <label for="lisensi" class="block text-sm font-medium text-gray-700">Lisensi</label>
                            <input type="text" name="lisensi" id="lisensi"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" id="password"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                        </div>

                        <!-- Konfirmasi Password -->
                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan
                    </button>
                    <button type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        id="btnCloseModal">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modalEdit" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal panel -->
        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="formEdit" action="" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="user_id" id="edit_user_id">

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Edit User</h3>
                        <p class="text-sm text-gray-500">Update informasi user</p>
                    </div>

                    <div class="space-y-4">
                        <!-- Role -->
                        <div>
                            <label for="edit_role_id" class="block text-sm font-medium text-gray-700">Role</label>
                            <select name="role_id" id="edit_role_id"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                                <option value="">Pilih Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Nama -->
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="name" id="edit_name"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                        </div>

                        <!-- NIP -->
                        <div>
                            <label for="edit_nip" class="block text-sm font-medium text-gray-700">NIP</label>
                            <input type="text" name="nip" id="edit_nip"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                        </div>

                        <!-- Lisensi -->
                        <div>
                            <label for="edit_lisensi" class="block text-sm font-medium text-gray-700">Lisensi</label>
                            <input type="text" name="lisensi" id="edit_lisensi"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="edit_email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="edit_email"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                required>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="edit_password" class="block text-sm font-medium text-gray-700">Password Baru
                                (Opsional)</label>
                            <input type="password" name="password" id="edit_password"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Biarkan kosong jika tidak ingin mengubah password</p>
                        </div>

                        <!-- Konfirmasi Password -->
                        <div>
                            <label for="edit_password_confirmation"
                                class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" id="edit_password_confirmation"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update
                    </button>
                    <button type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        id="btnCloseEditModal">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // === Konfigurasi Awal ===
        const modalTambah = document.getElementById('modalTambah');
        const formTambah = document.getElementById('formTambah');
        const btnCloseModalTambah = document.getElementById('btnCloseModal');

        const modalEdit = document.getElementById('modalEdit');
        const formEdit = document.getElementById('formEdit');
        const btnCloseModalEdit = document.getElementById('btnCloseEditModal');

        // === Fungsi Bantuan (Helpers) ===
        const showModal = (modal) => modal.classList.remove('hidden');
        const hideModal = (modal) => modal.classList.add('hidden');

        // Header untuk request fetch CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const fetchHeaders = {
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
            "Accept": "application/json",
        };

        // === Event Listener Utama (Event Delegation) ===
        document.body.addEventListener('click', function(e) {
            const target = e.target;

            if (target.closest('.btn-tambah')) {
                e.preventDefault();
                formTambah.reset();
                showModal(modalTambah);
            }

            if (target.closest('.btn-edit')) {
                e.preventDefault();
                const userRow = target.closest('[data-user-id]');
                const userId = userRow.getAttribute('data-user-id');
                handleEdit(userId);
            }

            if (target.closest('.btn-hapus')) {
                e.preventDefault();
                const userRow = target.closest('[data-user-id]');
                const userId = userRow.getAttribute('data-user-id');
                handleDelete(userId, userRow);
            }
        });

        // === Logika Modal & Form ===
        function handleEdit(userId) {
            Swal.fire({
                title: 'Memuat Data...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            // Pastikan URL fetch ini sesuai dengan route GET Anda
            fetch(`/users-management/update/${userId}`) // Sesuai dengan route name 'users-management.get'
                .then(response => {
                    if (!response.ok) throw new Error('Data user tidak ditemukan.');
                    return response.json();
                })
                .then(user => {
                    Swal.close();
                    // URL action untuk form submit, sesuai dengan route PUT
                    formEdit.action = `/users-management/update/${userId}`;
                    formEdit.querySelector('#edit_user_id').value = user.id;
                    formEdit.querySelector('#edit_name').value = user.name;
                    formEdit.querySelector('#edit_nip').value = user.nip;
                    formEdit.querySelector('#edit_lisensi').value = user.lisensi;
                    formEdit.querySelector('#edit_email').value = user.email;

                    // Mengisi role dengan cara yang lebih aman
                    if (user.roles && user.roles.length > 0) {
                        formEdit.querySelector('#edit_role_id').value = user.roles[0].id;
                    }

                    formEdit.querySelector('#edit_password').value = '';
                    formEdit.querySelector('#edit_password_confirmation').value = '';
                    showModal(modalEdit);
                })
                .catch(error => {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Gagal mengambil data user. ' + error.message });
                });
        }


        function handleDelete(userId, userRow) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/users-management/hapus/${userId}`, {
                        method: 'POST',
                        headers: fetchHeaders,
                        body: new URLSearchParams({ '_method': 'DELETE' })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Dihapus!', 'Data user berhasil dihapus.', 'success');
                            userRow.remove();
                        } else {
                            throw new Error(data.message || 'Gagal menghapus data.');
                        }
                    })
                    .catch(error => {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: error.message });
                    });
                }
            });
        }

        formTambah.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, { method: 'POST', headers: fetchHeaders, body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hideModal(modalTambah);
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'User baru berhasil ditambahkan.' })
                    .then(() => location.reload());
                } else {
                    const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Terjadi kesalahan.');
                    Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', html: `<pre style="text-align: left; white-space: pre-wrap;">${errors}</pre>` });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Oops...', text: 'Terjadi kesalahan teknis.' }));
        });

        formEdit.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, { method: 'POST', headers: fetchHeaders, body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hideModal(modalEdit);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data user berhasil diupdate.',
                        willClose: () => {
                            window.location.reload();
                        }
                    });
                } else {
                    const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Terjadi kesalahan.');
                    Swal.fire({ icon: 'error', title: 'Gagal Mengupdate', html: `<pre style="text-align: left; white-space: pre-wrap;">${errors}</pre>` });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Oops...', text: 'Terjadi kesalahan teknis. Periksa kembali backend Anda.' }));
        });


        [btnCloseModalTambah, btnCloseModalEdit].forEach(btn => {
            btn.addEventListener('click', () => { hideModal(modalTambah); hideModal(modalEdit); });
        });
        [modalTambah, modalEdit].forEach(modal => {
            modal.addEventListener('click', (e) => { if (e.target === modal) hideModal(modal); });
        });

        // Tab switching functionality with localStorage
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        // Hide all tab contents initially
        tabContents.forEach(content => content.classList.add('hidden'));

        // Reset all tab buttons to inactive state
        tabButtons.forEach(btn => {
            btn.classList.add('bg-transparent', 'hover:bg-gray-100', 'text-gray-600', 'hover:text-gray-800');
            btn.classList.remove('bg-blue-600', 'text-white');
        });

        // Get active tab from localStorage or use the first tab button's target
        const activeTab = localStorage.getItem('activeTab') || tabButtons[0].getAttribute('data-target');

        // Activate the correct tab
        const activeButton = document.querySelector(`[data-target="${activeTab}"]`);
        const activeContent = document.getElementById(activeTab);

        if (activeButton && activeContent) {
            activeButton.classList.remove('bg-transparent', 'hover:bg-gray-100', 'text-gray-600', 'hover:text-gray-800');
            activeButton.classList.add('bg-blue-600', 'text-white');
            activeContent.classList.remove('hidden');
        }

        // Add click handlers
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');

                // Save active tab to localStorage
                localStorage.setItem('activeTab', targetId);

                // Update tab buttons
                tabButtons.forEach(btn => {
                    btn.classList.remove('bg-blue-600', 'text-white');
                    btn.classList.add('bg-transparent', 'hover:bg-gray-100', 'text-gray-600', 'hover:text-gray-800');
                });
                this.classList.remove('bg-transparent', 'hover:bg-gray-100', 'text-gray-600', 'hover:text-gray-800');
                this.classList.add('bg-blue-600', 'text-white');

                // Update tab contents
                tabContents.forEach(content => content.classList.add('hidden'));
                document.getElementById(targetId).classList.remove('hidden');
            });
        });
    });
</script>

@endsection
