<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Informasi Pengguna</h3>
        <ul class="text-gray-600 space-y-1">
            <li><strong>Nama:</strong> {{ Auth::user()->name }}</li>
            <li><strong>NIP:</strong> {{ Auth::user()->nip ?? '-' }}</li>
            <li><strong>Email:</strong> {{ Auth::user()->email }}</li>
            <li><strong>Role:</strong> {{ Auth::user()->role->name }}</li>
        </ul>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Menu Cepat</h3>
        <div class="flex flex-wrap gap-2 text-center">
             <a href="{{ route('users-management.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 text-sm font-medium w-full">Manajemen Pengguna</a>
             <a href="{{ route('equipment-locations.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 text-sm font-medium w-full">Manajemen Equipment</a>
             <a href="{{ route('tenant-management.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 text-sm font-medium w-full">Manajemen Tenant</a>
        </div>
    </div>
</div>
