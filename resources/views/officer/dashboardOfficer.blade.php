@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 lg:mt-20">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            Dashboard
        </h1>
        <p class="text-gray-600 mb-6">
            Selamat datang, {{ Auth::user()->name }}!
        </p>

        <div class="bg-blue-50 p-4 rounded-lg mb-4">
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

        <div class="bg-green-50 p-4 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-green-800 mb-2">Menu Officer</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="#" class="bg-white p-3 rounded shadow hover:shadow-md transition-shadow">
                    <h4 class="font-semibold text-green-700">Daily Test Formulir</h4>
                    <p class="text-sm text-green-600">Lihat dan verifikasi laporan</p>
                </a>
                <a href="#" class="bg-white p-3 rounded shadow hover:shadow-md transition-shadow">
                    <h4 class="font-semibold text-green-700">Logbook Pos Jaga</h4>
                    <p class="text-sm text-green-600">Lihat dan verifikasi laporan</p>
                </a>
                <a href="#" class="bg-white p-3 rounded shadow hover:shadow-md transition-shadow">
                    <h4 class="font-semibold text-green-700">Check List CCTV</h4>
                    <p class="text-sm text-green-600">Lihat dan verifikasi laporan</p>
                </a>
            </div>
        </div>

        <div class="bg-red-50 p-4 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-red-800 mb-2">List Laporan di Tolak </h3>
        </div>
    </div>
</div>
@endsection
