@extends('layouts.app')

@section('title', 'Supervisor Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 lg:mt-20">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            Supervisor Dashboard
        </h1>
        <p class="text-gray-600 mb-6">
            Selamat datang, {{ Auth::user()->name }}! Anda login sebagai Supervisor.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Informasi Pengguna</h3>
                <ul class="text-blue-600">
                    <li><strong>Nama:</strong> {{ Auth::user()->name }}</li>
                    <li><strong>NIP:</strong> {{ Auth::user()->nip }}</li>
                    <li><strong>Email:</strong> {{ Auth::user()->email }}</li>
                    <li><strong>Role:</strong> {{ Auth::user()->role->name }}</li>
                    @if(Auth::user()->lisensi)
                        <li><strong>Lisensi:</strong> {{ Auth::user()->lisensi }}</li>
                    @endif
                </ul>
            </div>

            <div class="bg-purple-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-800 mb-2">Statistik Tim</h3>
                <ul class="text-purple-600">
                    <li><strong>Total Anggota Tim:</strong> 5</li>
                    <li><strong>Total Laporan:</strong> 15</li>
                    <li><strong>Laporan Pending:</strong> 3</li>
                    <li><strong>Laporan Selesai:</strong> 12</li>
                </ul>
            </div>
        </div>

        <div class="bg-green-50 p-4 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-green-800 mb-2">Menu Supervisor</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('supervisor.dailytest-form') }}" class="bg-white p-3 rounded shadow hover:shadow-md transition-shadow">
                    <h4 class="font-semibold text-green-700">Daily Test Formulir</h4>
                    <p class="text-sm text-green-600">Lihat dan verifikasi laporan</p>
                </a>
                <a href="{{ route('supervisor.logbook-form') }}" class="bg-white p-3 rounded shadow hover:shadow-md transition-shadow">
                    <h4 class="font-semibold text-green-700">Logbook Pos Jaga</h4>
                    <p class="text-sm text-green-600">Lihat dan verifikasi laporan</p>
                </a>
            </div>
        </div>

        {{-- <div class="bg-yellow-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-yellow-800 mb-2">Laporan Tim Terbaru</h3>
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">Petugas</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">Judul</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-2 px-4 border-b border-gray-200">1</td>
                        <td class="py-2 px-4 border-b border-gray-200">Ahmad</td>
                        <td class="py-2 px-4 border-b border-gray-200">Pemeriksaan Peralatan X-Ray</td>
                        <td class="py-2 px-4 border-b border-gray-200"><span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Menunggu Verifikasi</span></td>
                        <td class="py-2 px-4 border-b border-gray-200">2023-06-10</td>
                        <td class="py-2 px-4 border-b border-gray-200">
                            <a href="#" class="text-blue-500 hover:text-blue-700 mr-2">Lihat</a>
                            <a href="#" class="text-green-500 hover:text-green-700">Verifikasi</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 border-b border-gray-200">2</td>
                        <td class="py-2 px-4 border-b border-gray-200">Budi</td>
                        <td class="py-2 px-4 border-b border-gray-200">Pemeliharaan CCTV</td>
                        <td class="py-2 px-4 border-b border-gray-200"><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Terverifikasi</span></td>
                        <td class="py-2 px-4 border-b border-gray-200">2023-06-08</td>
                        <td class="py-2 px-4 border-b border-gray-200">
                            <a href="#" class="text-blue-500 hover:text-blue-700">Lihat</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div> --}}
    </div>
</div>
@endsection
