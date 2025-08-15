@extends('layouts.app')

@section('title', 'Loogbook Rotasi PSCP')

@section('content')
<div class="max-w-full mx-auto p-4">
    <h1 class="text-xl sm:text-2xl font-bold mb-4">Form Logbook Rotasi PSCP</h1>

    <form action="{{ route('logbook.store') }}" method="POST">
        @csrf

        <div class="overflow-x-auto">
            <table class="min-w-[1000px] w-full border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-2">No</th>
                        <th class="border p-2">Start</th>
                        <th class="border p-2">End</th>
                        <th class="border p-2">Pemeriksaan Dokumen</th>
                        <th class="border p-2">Pengatur Flow</th>
                        <th class="border p-2">Operator X-Ray</th>
                        <th class="border p-2 bg-blue-100">Pemeriksaan Orang Manual / HHMD</th>
                        <th class="border p-2 bg-blue-100">HHMD Random</th>
                        <th class="border p-2 bg-blue-100">HHMD Unpredictable</th>
                        <th class="border p-2 bg-yellow-100">Pemeriksa Manual Kabin</th>
                        <th class="border p-2 bg-yellow-100">Cek Random Barang</th>
                        <th class="border p-2 bg-yellow-100">Barang Unpredictable</th>
                        <th class="border p-2">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= 18; $i++) <tr>
                        <td class="border p-2 text-center">{{ $i }}</td>
                        <td class="border p-2">
                            <input type="time" name="rows[{{ $i }}][start]" class="w-full border-gray-300 rounded">
                        </td>
                        <td class="border p-2">
                            <input type="time" name="rows[{{ $i }}][end]" class="w-full border-gray-300 rounded">
                        </td>

                        <!-- Pemeriksaan Dokumen -->
                        <td class="border p-2">
                            <select name="rows[{{ $i }}][pemeriksaan_dokumen]"
                                class="officer-select w-full border-gray-300 rounded">
                                <option value="">Pilih Officer</option>
                                @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" data-nip="{{ $officer->nip }}">
                                    {{ $officer->name }}
                                </option>
                                @endforeach
                            </select>
                        </td>

                        <!-- Pengatur Flow -->
                        <td class="border p-2">
                            <select name="rows[{{ $i }}][pengatur_flow]"
                                class="officer-select w-full border-gray-300 rounded">
                                <option value="">Pilih Officer</option>
                                @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" data-nip="{{ $officer->nip }}">
                                    {{ $officer->name }}
                                </option>
                                @endforeach
                            </select>
                        </td>

                        <!-- Operator X-Ray -->
                        <td class="border p-2">
                            <select name="rows[{{ $i }}][operator_xray]"
                                class="officer-select w-full border-gray-300 rounded">
                                <option value="">Pilih Officer</option>
                                @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" data-nip="{{ $officer->nip }}">
                                    {{ $officer->name }}
                                </option>
                                @endforeach
                            </select>
                        </td>

                        <!-- Pemeriksaan Orang Manual / HHMD -->
                        <td class="border p-2 bg-blue-50">
                            <select name="rows[{{ $i }}][hhmd_petugas]"
                                class="officer-select w-full border-gray-300 rounded">
                                <option value="">Pilih Officer</option>
                                @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" data-nip="{{ $officer->nip }}">
                                    {{ $officer->name }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border p-2 bg-blue-50">
                            <input type="number" name="rows[{{ $i }}][hhmd_random]" min="0"
                                class="w-full border-gray-300 rounded">
                        </td>
                        <td class="border p-2 bg-blue-50">
                            <input type="number" name="rows[{{ $i }}][hhmd_unpredictable]" min="0"
                                class="w-full border-gray-300 rounded">
                        </td>

                        <!-- Pemeriksa Manual Kabin -->
                        <td class="border p-2 bg-yellow-50">
                            <select name="rows[{{ $i }}][manual_kabin_petugas]"
                                class="officer-select w-full border-gray-300 rounded">
                                <option value="">Pilih Officer</option>
                                @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" data-nip="{{ $officer->nip }}">
                                    {{ $officer->name }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border p-2 bg-yellow-50">
                            <input type="number" name="rows[{{ $i }}][cek_random_barang]" min="0"
                                class="w-full border-gray-300 rounded">
                        </td>
                        <td class="border p-2 bg-yellow-50">
                            <input type="number" name="rows[{{ $i }}][barang_unpredictable]" min="0"
                                class="w-full border-gray-300 rounded">
                        </td>

                        <!-- Keterangan -->
                        <td class="border p-2">
                            <input type="text" name="rows[{{ $i }}][keterangan]" class="w-full border-gray-300 rounded">
                        </td>
                        </tr>
                        @endfor
                </tbody>
            </table>
        </div>

        <!-- Submit -->
        <div class="mt-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.officer-select').forEach(select => {
        select.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                // Ganti teks option yang dipilih dengan NIP
                selectedOption.textContent = selectedOption.getAttribute('data-nip').slice(-4);
            }
        });

        // Untuk mengembalikan tampilan ke nama jika dropdown di-reset
        select.addEventListener('focus', function() {
            Array.from(this.options).forEach(option => {
                if (option.value && option.textContent === option.getAttribute('data-nip')) {
                    option.textContent = option.dataset.originalText || option.getAttribute('data-original-text');
                }
            });
        }, true);

        // Simpan teks asli (nama) di atribut data
        Array.from(select.options).forEach(option => {
            if (option.value) {
                option.dataset.originalText = option.textContent;
            }
        });
    });
});
</script>
@endsection
