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

        <div class="bg-green-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-green-800 mb-2">Menu</h3>
            <ul class="text-green-600">
                <li><a href="#" class="underline">Lihat Laporan</a></li>
                <li><a href="#" class="underline">Buat Laporan Baru</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection