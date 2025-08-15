@extends('layouts.app')

@section('title', 'Logbook Rotasi PSCP')

@section('content')
<div class="max-w-full mx-auto p-4 lg:mt-16">
    <h1 class="text-xl sm:text-2xl font-bold mb-4">Form Logbook Rotasi PSCP</h1>

    <!-- Status indicator -->
    <div id="saveStatus" class="mb-4 p-2 rounded hidden">
        <span id="statusText"></span>
    </div>

    <form id="logbookForm" action="{{ route('logbook.rotasi-pscp.submit') }}" method="POST">
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
                <tbody id="logbookTableBody">
                    @for($i = 1; $i <= 18; $i++)
                    <tr data-row="{{ $i }}" data-row-id="">
                        <td class="border p-2 text-center">{{ $i }}</td>
                        <td class="border p-2">
                            <input type="time"
                                   name="rows[{{ $i }}][start]"
                                   data-field="start"
                                   class="w-full border-gray-300 rounded autosave-field">
                        </td>
                        <td class="border p-2">
                            <input type="time"
                                   name="rows[{{ $i }}][end]"
                                   data-field="end"
                                   class="w-full border-gray-300 rounded autosave-field">
                        </td>

                        <!-- Pemeriksaan Dokumen -->
                        <td class="border p-2">
                            <select name="rows[{{ $i }}][pemeriksaan_dokumen]"
                                    data-field="pemeriksaan_dokumen"
                                    class="officer-select w-full border-gray-300 rounded autosave-field">
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
                                    data-field="pengatur_flow"
                                    class="officer-select w-full border-gray-300 rounded autosave-field">
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
                                    data-field="operator_xray"
                                    class="officer-select w-full border-gray-300 rounded autosave-field">
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
                                    data-field="hhmd_petugas"
                                    class="officer-select w-full border-gray-300 rounded autosave-field">
                                <option value="">Pilih Officer</option>
                                @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" data-nip="{{ $officer->nip }}">
                                    {{ $officer->name }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border p-2 bg-blue-50">
                            <input type="number"
                                   name="rows[{{ $i }}][hhmd_random]"
                                   data-field="hhmd_random"
                                   min="0"
                                   class="w-full border-gray-300 rounded autosave-field">
                        </td>
                        <td class="border p-2 bg-blue-50">
                            <input type="number"
                                   name="rows[{{ $i }}][hhmd_unpredictable]"
                                   data-field="hhmd_unpredictable"
                                   min="0"
                                   class="w-full border-gray-300 rounded autosave-field">
                        </td>

                        <!-- Pemeriksa Manual Kabin -->
                        <td class="border p-2 bg-yellow-50">
                            <select name="rows[{{ $i }}][manual_kabin_petugas]"
                                    data-field="manual_kabin_petugas"
                                    class="officer-select w-full border-gray-300 rounded autosave-field">
                                <option value="">Pilih Officer</option>
                                @foreach($officers as $officer)
                                <option value="{{ $officer->id }}" data-nip="{{ $officer->nip }}">
                                    {{ $officer->name }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border p-2 bg-yellow-50">
                            <input type="number"
                                   name="rows[{{ $i }}][cek_random_barang]"
                                   data-field="cek_random_barang"
                                   min="0"
                                   class="w-full border-gray-300 rounded autosave-field">
                        </td>
                        <td class="border p-2 bg-yellow-50">
                            <input type="number"
                                   name="rows[{{ $i }}][barang_unpredictable]"
                                   data-field="barang_unpredictable"
                                   min="0"
                                   class="w-full border-gray-300 rounded autosave-field">
                        </td>

                        <!-- Keterangan -->
                        <td class="border p-2">
                            <input type="text"
                                   name="rows[{{ $i }}][keterangan]"
                                   data-field="keterangan"
                                   class="w-full border-gray-300 rounded autosave-field">
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Submit -->
        <div class="mt-4 ">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let autosaveTimeout;
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                      document.querySelector('input[name="_token"]')?.value;

    // Load draft saat halaman dimuat
    loadDraft();

    // Setup officer select behavior
    setupOfficerSelects();

    // Setup autosave
    setupAutosave();

    function setupOfficerSelects() {
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
                    if (option.value && option.textContent === option.getAttribute('data-nip')?.slice(-4)) {
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
    }

    function setupAutosave() {
        // Listen untuk perubahan pada semua field yang perlu di-autosave
        document.querySelectorAll('.autosave-field').forEach(field => {
            field.addEventListener('change', handleFieldChange);
            field.addEventListener('input', handleFieldInput);
        });
    }

    function handleFieldChange(event) {
        clearTimeout(autosaveTimeout);
        autosaveTimeout = setTimeout(() => {
            autosaveRow(event.target);
        }, 500); // Delay 500ms setelah user berhenti mengetik
    }

    function handleFieldInput(event) {
        // Untuk input type text dan number, gunakan debounce yang lebih lama
        if (event.target.type === 'text' || event.target.type === 'number') {
            clearTimeout(autosaveTimeout);
            autosaveTimeout = setTimeout(() => {
                autosaveRow(event.target);
            }, 1000); // Delay 1 detik untuk text input
        }
    }

    function autosaveRow(field) {
        const row = field.closest('tr');
        const rowNumber = row.dataset.row;
        const rowId = row.dataset.rowId || null;

        // Kumpulkan semua data dari baris ini
        const rowData = {
            row_id: rowId,
            start: row.querySelector('[data-field="start"]')?.value || null,
            end: row.querySelector('[data-field="end"]')?.value || null,
            pemeriksaan_dokumen: row.querySelector('[data-field="pemeriksaan_dokumen"]')?.value || null,
            pengatur_flow: row.querySelector('[data-field="pengatur_flow"]')?.value || null,
            operator_xray: row.querySelector('[data-field="operator_xray"]')?.value || null,
            hhmd_petugas: row.querySelector('[data-field="hhmd_petugas"]')?.value || null,
            hhmd_random: row.querySelector('[data-field="hhmd_random"]')?.value || null,
            hhmd_unpredictable: row.querySelector('[data-field="hhmd_unpredictable"]')?.value || null,
            manual_kabin_petugas: row.querySelector('[data-field="manual_kabin_petugas"]')?.value || null,
            cek_random_barang: row.querySelector('[data-field="cek_random_barang"]')?.value || null,
            barang_unpredictable: row.querySelector('[data-field="barang_unpredictable"]')?.value || null,
            keterangan: row.querySelector('[data-field="keterangan"]')?.value || null,
        };

        // Cek apakah ada data yang perlu disimpan (tidak semua field kosong)
        const hasData = Object.values(rowData).some(value => value !== null && value !== '');

        if (!hasData) {
            return; // Tidak ada data untuk disimpan
        }

        showSaveStatus('Menyimpan...', 'bg-yellow-100 text-yellow-800');

        // Kirim request autosave
        fetch('/logbook/rotasi-pscp/autosave', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify(rowData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update row ID jika ini adalah insert baru
                if (data.id && !row.dataset.rowId) {
                    row.dataset.rowId = data.id;
                }
                showSaveStatus('Tersimpan otomatis', 'bg-green-100 text-green-800');
            } else {
                showSaveStatus('Gagal menyimpan', 'bg-red-100 text-red-800');
            }
        })
        .catch(error => {
            console.error('Autosave error:', error);
            showSaveStatus('Gagal menyimpan', 'bg-red-100 text-red-800');
        });
    }

    function loadDraft() {
        showSaveStatus('Memuat data...', 'bg-blue-100 text-blue-800');

        fetch('/logbook/rotasi-pscp/load-draft', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                // Populate form dengan data yang ada
                data.forEach((detail, index) => {
                    const row = document.querySelector(`tr[data-row="${index + 1}"]`);
                    if (row) {
                        // Set row ID
                        row.dataset.rowId = detail.id;

                        // Set nilai untuk setiap field
                        const fields = [
                            'start', 'end', 'pemeriksaan_dokumen', 'pengatur_flow', 'operator_xray',
                            'hhmd_petugas', 'hhmd_random', 'hhmd_unpredictable',
                            'manual_kabin_petugas', 'cek_random_barang', 'barang_unpredictable', 'keterangan'
                        ];

                        fields.forEach(fieldName => {
                            const field = row.querySelector(`[data-field="${fieldName}"]`);
                            if (field && detail[fieldName] !== null && detail[fieldName] !== undefined) {
                                field.value = detail[fieldName];

                                // Untuk select officer, trigger change event untuk menampilkan NIP
                                if (field.classList.contains('officer-select')) {
                                    field.dispatchEvent(new Event('change'));
                                }
                            }
                        });
                    }
                });
                showSaveStatus('Data dimuat', 'bg-green-100 text-green-800');
            } else {
                showSaveStatus('Tidak ada data tersimpan', 'bg-gray-100 text-gray-800');
            }

            // Hide status setelah 3 detik
            setTimeout(hideSaveStatus, 3000);
        })
        .catch(error => {
            console.error('Load draft error:', error);
            showSaveStatus('Gagal memuat data', 'bg-red-100 text-red-800');
            setTimeout(hideSaveStatus, 3000);
        });
    }

    function showSaveStatus(message, className) {
        const statusDiv = document.getElementById('saveStatus');
        const statusText = document.getElementById('statusText');

        statusText.textContent = message;
        statusDiv.className = `mb-4 p-2 rounded ${className}`;
        statusDiv.classList.remove('hidden');

        // Auto hide setelah 2 detik (kecuali untuk pesan loading)
        if (!message.includes('...')) {
            setTimeout(hideSaveStatus, 2000);
        }
    }

    function hideSaveStatus() {
        const statusDiv = document.getElementById('saveStatus');
        statusDiv.classList.add('hidden');
    }
});
</script>
@endsection
