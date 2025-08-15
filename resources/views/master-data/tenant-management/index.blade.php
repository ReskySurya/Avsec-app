@extends('layouts.app')

@section('title', 'Tenant Management')

@section('content')
<div
    x-data="{
            openTenant: false,
            openEditTenant: false, 
            editTenantData: { 
                    tenantID: '', 
                    tenant_name: '' 
                },
            }" class="mx-auto  p-6 min-h-screen" class=" mx-auto sm:mt-6 lg:mt-20 px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class=" mt-20">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
            <div class="">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Tenant</h1>
                <p class="text-gray-600">Kelola Tenant dengan mudah</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3000)"
        class="bg-gradient-to-r from-green-400 to-green-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg border-l-4 border-green-600 animate-pulse">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3000)"
        class="bg-gradient-to-r from-red-400 to-red-500 text-white px-6 py-4 rounded-xl mb-6 shadow-lg border-l-4 border-red-600">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
    </div>
    @endif

    {{-- Tenant Section --}}
    <div
        class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-2xl mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-6 py-6 text-white">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div class="mb-4 sm:mb-0">
                    <h3 class="text-2xl font-bold mb-1">Daftar Tenant</h3>
                    <p class="text-blue-100">Kelola list Tenant</p>
                </div>
                <button @click="openTenant = true"
                    class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl w-full sm:w-auto">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Tenant
                </button>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 md:hidden">
            @forelse($tenantList ?? [] as $index => $tenant)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="p-5 flex justify-between">
                    <h4 class="text-lg font-bold text-gray-800 truncate">{{ $tenant->tenantID }}</h4>
                    <p class="text-gray-500 mt-2"># {{ $index + 1 }}</p>
                </div>
                <div>
                    <span class="px-5 py-3 text-gray-700">{{ $tenant->tenant_name }}</span>
                </div>
                <div class="bg-gray-50 px-5 py-3 flex justify-end space-x-2">
                    <button type="button" @click="
                        openEditTenant = true;
                        editTenantData.tenantID = '{{ $tenant->tenantID }}';
                        editTenantData.tenant_name = '{{ $tenant->tenant_name }}';
                        "
                        class="p-2 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <form action="{{ route('tenant.destroy',$tenant->tenantID) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Yakin ingin menghapus?')"
                            class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-1 sm:col-span-2 text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-500 text-lg">Belum ada data Equipment</p>
                <p class="text-gray-400">Tambahkan tenant pertama Anda</p>
            </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="overflow-x-auto p-6 hidden md:block">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-50 to-cyan-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ID Tenant</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Tenant</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($tenantList ?? [] as $index => $tenant)
                    <tr class="hover:bg-gray-50 transition-all duration-200 cursor-pointer" 
                    @click="window.location.href='{{ route('tenant.items', ['tenantID' => $tenant->tenantID]) }}'"
                    >
                        <td class="px-6 py-4 whitespace-nowrap  font-medium">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $tenant->tenantID }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $tenant->tenant_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button type="button"
                                class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-4 py-2 rounded-lg font-medium transition-colors duration-200"
                                @@click.stop="
                                        openEditTenant = true;
                                        editTenantData.tenantID = '{{ $tenant->tenantID }}';
                                        editTenantData.tenant_name = '{{ $tenant->tenant_name }}';
                                    ">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Edit
                            </button>
                            <form action="{{ route('tenant.destroy',$tenant->tenantID) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit"  onclick="return confirm('Yakin ingin menghapus?')"
                                    class="bg-red-100 text-red-600 hover:bg-red-200 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-500 text-lg font-medium mb-1">Belum ada data Tenant</p>
                            <p class="text-gray-400">Tambahkan tenant pertama Anda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- add Tenant -->
    <div x-show="openTenant" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openTenant = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-cyan-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Tambah Tenant</h2>
                <p class="text-blue-100">Tambahkan Tenant baru</p>
            </div>
            <form action="{{ route('tenant.store') }}" method="POST" class="p-6">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Tenant</label>
                    <input type="text" name="tenant_name" required
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200"
                        value="{{ old('tenant_name') }}" placeholder="Masukkan nama tenant">
                    @error('tenant_name')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openTenant = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-600 text-white rounded-xl hover:from-blue-600 hover:to-cyan-700 transition-all duration-200 font-medium shadow-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- edit Tenant -->
    <div x-show="openEditTenant" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openEditTenant = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-500 to-cyan-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">Edit Tenant</h2>
                <p class="text-blue-100">Ubah informasi tenant</p>
            </div>
            <form
                :action="'/tenant-management/update/' + editTenantData.tenantID"
                method="POST" class="p-6">
                @csrf
                @method('POST')
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Tenant</label>
                    <input type="text" name="tenant_name" required x-model="editTenantData.tenant_name"
                        class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl focus:border-blue-500 focus:outline-none transition-colors duration-200"
                        placeholder="Masukkan nama tenant">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="openEditTenant = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-600 text-white rounded-xl hover:from-blue-600 hover:to-cyan-700 transition-all duration-200 font-medium shadow-lg">
                        Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection