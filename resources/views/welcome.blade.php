@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            Welcome to Laravel with Tailwind CSS
        </h1>
        <p class="text-gray-600 mb-6">
            Proyek Laravel Anda dengan Tailwind CSS sudah siap digunakan!
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Documentation</h3>
                <p class="text-blue-600">Laravel memiliki dokumentasi yang lengkap dan mudah dipahami.</p>
            </div>

            <div class="bg-green-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-green-800 mb-2">Laracasts</h3>
                <p class="text-green-600">Video tutorial terbaik untuk belajar Laravel dari dasar.</p>
            </div>

            <div class="bg-purple-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-800 mb-2">Vibrant Ecosystem</h3>
                <p class="text-purple-600">Ekosistem Laravel yang kaya dengan berbagai package.</p>
            </div>
        </div>

        <div class="mt-8">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Get Started
            </button>
            <button class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-2">
                Learn More
            </button>
        </div>
    </div>
</div>
@endsection
