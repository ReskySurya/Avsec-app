@extends('layouts.app')

@section('content')
<div class="container mx-auto px-2 py-4">
    <div class="bg-white shadow-md rounded px-4 sm:px-8 pt-6 pb-8 mb-4 w-full max-w-full">
        <h1 class="text-xl sm:text-2xl font-bold mb-4">Tinjau Formulir HHMD</h1>

        <div class="bg-white p-4 w-full max-w-full">
            <div id="format" class="mx-auto w-full">
                <div class="border-t-2 border-x-2 border-black bg-white shadow-md p-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between">
                        <img src="{{ asset('images/airport-security-logo.png') }}" alt="Logo"
                            class="w-20 h-20 mb-2 sm:mb-0">
                        <h1 class="text-sm sm:text-xl font-bold text-center flex-grow px-2">
                            CHECK LIST PENGUJIAN HARIAN<br>
                            PENDETEKSI LOGAM GENGGAM<br>
                            (HAND HELD METAL DETECTOR/HHMD)<br>
                            PADA KONDISI NORMAL (HIJAU)
                        </h1>
                        <img src="{{ asset('images/injourney-logo.png') }}" alt="Injourney Logo"
                            class="w-20 h-20 mt-2 sm:mt-0">
                    </div>
                </div>

                <div class="border-2 border-black bg-white shadow">
                    <table class="w-full text-xs sm:text-sm">
                        <tbody>
                            <tr class="border-b border-black">
                                <th class="w-1/3 text-left p-1 sm:p-2">Nama Operator Penerbangan:</th>
                                <td class="w-2/3 p-2">
                                    <input type="text" value="Bandar Udara Adisutjipto Yogyakarta" readonly>
                                </td>
                            </tr>
                            <tr class="border-b border-black">
                                <th class="w-1/3 text-left p-2">Tanggal & Waktu Pengujian:</th>
                                <td class="w-2/3 p-2">{{ date('d-m-Y H:i', strtotime($form->testDate)) . ' WIB' }}</td>
                            </tr>
                            <tr class="border-b border-black">
                                <th class="w-1/3 text-left p-2">Lokasi Penempatan:</th>
                                <td class="w-2/3 p-2">{{ $form->location->name }}</td>
                            </tr>
                            <tr class="border-b border-black">
                                <th class="w-1/3 text-left p-2">Merk/Tipe/Nomor Seri:</th>
                                <td class="w-2/3 p-2">{{ $form->deviceInfo }}</td>
                            </tr>
                            <tr class="border-b border-black">
                                <th class="w-1/3 text-left p-2">Nomor dan Tanggal Sertifikat:</th>
                                <td class="w-2/3 p-2">{{ $form->certificateInfo }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="px-4">
                        <div class="p-2">
                            <div class="mb-0">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" {{ $form->reportDetails->first()->terpenuhi ? 'checked' : '' }} disabled>
                                    <span class="ml-2 text-sm">Terpenuhi</span>
                                </label>
                            </div>
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" {{ $form->reportDetails->first()->tidakterpenuhi ? 'checked' : '' }} disabled>
                                    <span class="ml-2 text-sm">Tidak Terpenuhi</span>
                                </label>
                            </div>
                        </div>
                        <div class="border-x-2 border-t-2 border-black text-center items-center pt-10">
                            <div>
                                <h2 class="font-bold mb-2">TEST 1</h2>
                                <div class="w-20 h-20 mx-auto border-2 border-black flex items-center justify-center">
                                    <input type="checkbox" {{ $form->reportDetails->first()->test1 ? 'checked' : '' }} disabled>
                                </div>
                            </div>
                        </div>

                        <div class="border-x-2 border-black pt-10 pb-10">
                            <div class="flex items-center mb-0 pl-4">
                                <input type="checkbox" {{ $form->reportDetails->first()->testCondition1 ? 'checked' : '' }} disabled>
                                <label class="ml-2 text-sm">Letak alat uji OTP dan HHMD pada saat pengujian harus > 1m
                                    dari benda logam lain disekelilingnya.</label>
                            </div>
                            <div class="flex items-center mb-0 pl-4">
                                <input type="checkbox" {{ $form->reportDetails->first()->testCondition2 ? 'checked' : '' }} disabled>
                                <label class="ml-2 text-sm">Jarak antara HHMD dan OTP > 3-5 cm.</label>
                            </div>
                        </div>
                    </div>

                    <div class="border-t-2 border-black p-4">
                        <div class="flex items-start mb-2">
                            <label class="text-gray-700 font-bold mr-4">Hasil:</label>
                            <div class="flex flex-col">
                                <div class="flex items-center mb-0">
                                    <input type="radio" {{ $form->result == 'pass' ? 'checked' : '' }} disabled>
                                    <label class="text-sm ml-2">PASS</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" {{ $form->result == 'fail' ? 'checked' : '' }} disabled>
                                    <label class="text-sm ml-2">FAIL</label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">CATATAN:</label>
                            <p>{{ $form->notes }}</p>
                        </div>
                    </div>

                    <div class="border-t-2 border-black p-2 sm:p-4">
                        <h3 class="text-xs sm:text-sm font-bold mb-2">Personel Pengamanan Penerbangan</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4">
                            <div class="grid grid-rows-2 gap-1 sm:gap-2 items-center text-center">
                                <div class="text-center self-end">
                                    <h4 class="font-bold">{{ $form->submittedBy->name }}</h4>
                                    <label class="text-gray-700 font-normal text-xs sm:text-sm">1. Airport Security
                                        Officer</label>
                                </div>
                                <div class="text-center self-end">
                                    <h4 class="font-bold">
                                        {{ Auth::user()->name }}
                                    </h4>
                                    <label class="text-gray-700 font-normal text-xs sm:text-sm">2. Airport Security
                                        Supervisor</label>
                                </div>
                            </div>
                            <div>
                                <div class="flex flex-col items-center">
                                    @if($form->submitterSignature)
                                    <img src="{{ $form->submitterSignature }}" alt="Tanda tangan Officer"
                                        class="max-w-full h-auto">
                                    @else
                                    <p class="text-xs sm:text-sm">Tanda tangan Officer tidak tersedia</p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-center mt-2 sm:mt-4">
                                    @if($form->approverSignature)
                                    <img src="{{ $form->approverSignature }}" alt="Tanda tangan Supervisor"
                                        id="supervisorSignatureImage" class="max-w-full h-auto">
                                    @else
                                    <p class="text-xs sm:text-sm">Tanda tangan Supervisor tidak tersedia</p>
                                    @endif
                                </div>
                                @if(!$form->approverSignature)
                                <div class="flex flex-col items-center mt-2 sm:mt-4" id="signatureContainer">
                                    <h3 class="text-xs sm:text-sm font-bold mb-2">Tanda Tangan Supervisor</h3>
                                    <canvas id="signatureCanvas" class="border border-black rounded-md w-full"
                                        width="300" height="150"></canvas>
                                    <div class="mt-2 flex justify-start space-x-2">
                                        <button type="button" id="clearSignature"
                                            class="bg-slate-200 border border-black text-black px-2 py-1 sm:px-4 sm:py-2 rounded text-xs sm:text-base">Clear</button>
                                        <button type="button" id="saveSupervisorSignature"
                                            class="bg-slate-200 border border-black text-black px-2 py-1 sm:px-4 sm:py-2 rounded text-xs sm:text-base">Save</button>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('hhmd.updateStatus', $form->reportID) }}" method="POST" class="mt-2 sm:mt-4"
            id="hhmdForm">
            @csrf
            @method('PATCH')
            <div class="mb-2 sm:mb-4">
                <label class="block text-gray-700 text-xs sm:text-sm font-bold mb-1 sm:mb-2" for="status_id">
                    Status
                </label>

                @php
                $statuses = \App\Models\ReportStatus::select('id', 'name', 'label')->orderBy('id')->get();
                @endphp

                <select name="status_id" id="status_id"
                    class="w-full border rounded px-1 py-1 sm:px-2 sm:py-1 text-xs sm:text-base">
                    @php
                    $rejectedStatus = $statuses->firstWhere('name', 'rejected');
                    @endphp
                    @foreach($statuses as $status)
                    <option value="{{ $status->id }}" {{ $form->status_id == $status->id ? 'selected' : '' }}
                        data-requires-note="{{ $status->name === 'rejected' ? 'true' : 'false' }}">
                        {{ $status->label }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div id="rejectionNoteContainer" class="mb-4 {{ $form->isRejected() ? '' : 'hidden' }}">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="approvalNote">
                    Catatan Penolakan
                </label>
                <textarea name="approvalNote" id="approvalNote" rows="4"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Masukkan alasan penolakan..." {{
                    $form->isRejected() ? 'required' : '' }}>{{ old('approvalNote', $form->approvalNote) }}</textarea>
                @error('approvalNote')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button id="updateStatusButton"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-2 py-1 sm:px-4 sm:py-2 rounded text-xs sm:text-base disabled:opacity-50 disabled:cursor-not-allowed"
                    type="submit" title="Harap simpan tanda tangan terlebih dahulu">
                    Perbarui Status
                </button>
                <a href="#"
                    class="text-xs sm:text-sm font-bold text-blue-500 hover:text-blue-800">
                    Kembali ke Dashboard
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('hhmdForm');
        const submitButton = document.getElementById('updateStatusButton');
        const statusSelect = document.getElementById('status_id');
        const rejectionNoteContainer = document.getElementById('rejectionNoteContainer');
        const rejectionNote = document.getElementById('approvalNote');

        // Handle status change to show/hide rejection note
        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const requiresNote = selectedOption.dataset.requiresNote === 'true';

                if (requiresNote) {
                    rejectionNoteContainer.classList.remove('hidden');
                    rejectionNote.setAttribute('required', 'required');
                } else {
                    rejectionNoteContainer.classList.add('hidden');
                    rejectionNote.removeAttribute('required');
                }

                updateSubmitButtonState();
            });
        }
        const signatureContainer = document.getElementById('signatureContainer');
        const canvas = document.getElementById('signatureCanvas');
        const ctx = canvas.getContext('2d');
        const rejectionNoteTextarea = document.getElementById('approvalNote');

        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

        // Event handler untuk status select sudah ditangani di atas

        function updateSubmitButtonState() {
            const supervisorSignatureImage = document.getElementById('supervisorSignatureImage');
            const hasSignature = supervisorSignatureImage !== null;

            if (submitButton) {
                submitButton.disabled = !hasSignature;
                submitButton.classList.toggle('opacity-50', !hasSignature);
                submitButton.classList.toggle('cursor-not-allowed', !hasSignature);
                submitButton.title = hasSignature ? '' : 'Harap simpan tanda tangan terlebih dahulu';
            }
        }

        updateSubmitButtonState();

        // Touch events for mobile
        canvas.addEventListener('touchstart', handleTouchStart, { passive: false });
        canvas.addEventListener('touchmove', handleTouchMove, { passive: false });
        canvas.addEventListener('touchend', stopDrawing);

        // Mouse events for desktop
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        // Clear and Save buttons
        document.getElementById('clearSignature').addEventListener('click', clearCanvas);
        document.getElementById('saveSupervisorSignature').addEventListener('click', saveSupervisorSignature);

        function handleTouchStart(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const rect = canvas.getBoundingClientRect();
            lastX = touch.clientX - rect.left;
            lastY = touch.clientY - rect.top;
            isDrawing = true;
        }

        function handleTouchMove(e) {
            e.preventDefault();
            if (!isDrawing) return;

            const touch = e.touches[0];
            const rect = canvas.getBoundingClientRect();
            const currentX = touch.clientX - rect.left;
            const currentY = touch.clientY - rect.top;

            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000';

            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(currentX, currentY);
            ctx.stroke();

            lastX = currentX;
            lastY = currentY;
        }

        function startDrawing(e) {
            isDrawing = true;
            const rect = canvas.getBoundingClientRect();
            lastX = e.clientX - rect.left;
            lastY = e.clientY - rect.top;
        }

        function draw(e) {
            if (!isDrawing) return;

            const rect = canvas.getBoundingClientRect();
            const currentX = e.clientX - rect.left;
            const currentY = e.clientY - rect.top;

            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000';

            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(currentX, currentY);
            ctx.stroke();

            lastX = currentX;
            lastY = currentY;
        }

        function stopDrawing() {
            isDrawing = false;
            ctx.beginPath();
        }

        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        // Helper function to check if canvas is empty
        function isCanvasEmpty(canvas) {
            const context = canvas.getContext('2d');
            const pixelBuffer = new Uint32Array(
                context.getImageData(0, 0, canvas.width, canvas.height).data.buffer
            );
            return !pixelBuffer.some(color => color !== 0);
        }

        // Form submission handler
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            // Validasi tanda tangan untuk semua status
            const supervisorSignatureImage = document.getElementById('supervisorSignatureImage');
            const statusSelect = document.getElementById('status_id');
            const selectedOption = statusSelect.options[statusSelect.selectedIndex];
            const requiresNote = selectedOption.dataset.requiresNote === 'true';
            const rejectionNote = document.getElementById('approvalNote').value.trim();

            // Validate rejection note if required
            if (requiresNote && !rejectionNote) {
                alert('Harap isi catatan penolakan');
                return false;
            }
            if (!supervisorSignatureImage) {
                Swal.fire({
                    title: 'Tanda Tangan Diperlukan!',
                    text: 'Anda harus menyimpan tanda tangan terlebih dahulu sebelum memperbarui status.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return false;
            }

            if (statusSelect.value === 'rejected') {
                // Validasi rejection note
                if (!rejectionNoteTextarea.value.trim()) {
                    Swal.fire({
                        title: 'Catatan Diperlukan!',
                        text: 'Silakan isi catatan penolakan.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                    rejectionNoteTextarea.focus();
                    return false;
                }
            }

            // Submit form jika semua validasi berhasil
            this.submit();
        });

        // Fungsi save tanda tangan hanya fokus pada penyimpanan tanda tangan
        function saveSupervisorSignature() {
            const supervisorSignatureData = canvas.toDataURL('image/png');

            if (isCanvasEmpty(canvas)) {
                Swal.fire({
                    title: 'Tanda Tangan Kosong!',
                    text: 'Silakan buat tanda tangan terlebih dahulu.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Menampilkan tanda tangan yang disimpan
            const signatureContainer = document.getElementById('signatureContainer');
            signatureContainer.innerHTML = `
                <h3 class="text-xs sm:text-sm font-bold mb-2">Tanda Tangan Supervisor</h3>
                <img src="${supervisorSignatureData}" alt="Tanda tangan Supervisor" id="supervisorSignatureImage" class="max-w-full h-auto">
            `;

            // Mengirim data tanda tangan ke server
            fetch('{{ route("hhmd.saveSupervisorSignature", $form->reportID) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ signature: supervisorSignatureData })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Tanda tangan berhasil disimpan',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                    // Enable tombol perbarui status
                    updateSubmitButtonState();
                } else {
                    throw new Error('Gagal menyimpan tanda tangan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menyimpan tanda tangan',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            });
        }

        // Handle window resize
        function resizeCanvas() {
            const container = canvas.parentElement;
            canvas.width = container.offsetWidth;
            canvas.height = 150;

            // Reset context properties after resize
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000';
        }

        // Call resizeCanvas on page load and window resize
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
    });
</script>
@endsection
