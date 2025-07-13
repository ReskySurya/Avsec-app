@if(!isset($isPdf))
@extends('layouts.app')

@section('content')
@endif


<div class="bg-white-100 px-4 sm:px-8 md:px-16 lg:px-32 xl:px-64">
    <div>
        <x-form-wtmd :wtmdLocations="$wtmdLocations" />
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Set current date and time
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        const formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
        document.getElementById('testDateTime').value = formattedDateTime;

        // Validasi form dan tanda tangan
        const form = document.getElementById('wtmdForm');
        const submitButton = document.getElementById('submitButton');
        const buttonText = document.getElementById('buttonText');
        const buttonLoading = document.getElementById('buttonLoading');

        // Fungsi untuk mengecek lokasi
        

        // Event listener untuk form submission
        document.getElementById('wtmdForm').addEventListener('submit', async function (e) {
            e.preventDefault();


            // Validasi lokasi
            const locationSelect = document.getElementById('location');
            const locationValue = locationSelect.value;

            if (!locationValue) {
                Swal.fire({
                    title: 'Error',
                    text: 'Silakan pilih lokasi terlebih dahulu',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            try {
                

                // Validasi tanda tangan
                const signatureData = document.getElementById('submitterSignatureData').value;
                if (!signatureData) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Silakan tambahkan tanda tangan officer',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Validasi supervisor
                const supervisorId = document.getElementById('approvedByID').value;
                if (!supervisorId) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Silakan pilih supervisor',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Tampilkan loading state
                const submitButton = document.getElementById('submitButton');
                const buttonText = document.getElementById('buttonText');
                const buttonLoading = document.getElementById('buttonLoading');

                buttonText.classList.add('hidden');
                buttonLoading.classList.remove('hidden');
                submitButton.disabled = true;

                // Submit form
                const formData = new FormData(this);

                const response = await fetch('/daily-test/wtmd/store', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();

                console.log('Form submission result:', formData, result);
                if (response.ok) {
                    alert(locationValue);
                    Swal.fire({
                        title: 'Berhasil',
                        text: result.message || 'Form berhasil dikirim',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = result.redirect || '/dashboard/officer';
                    });
                } else {
                    throw new Error(result.message || 'Terjadi kesalahan saat mengirim form');
                }
            } catch (error) {
                console.error('Error submitting form:', error);

                Swal.fire({
                    title: 'Error',
                    text: error.message || 'Terjadi kesalahan saat mengirim form',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } finally {
                // Reset button state
                const buttonText = document.getElementById('buttonText');
                const buttonLoading = document.getElementById('buttonLoading');
                const submitButton = document.getElementById('submitButton');

                buttonText.classList.remove('hidden');
                buttonLoading.classList.add('hidden');
                submitButton.disabled = false;
            }
        });

        // Event listener untuk perubahan lokasi
        document.getElementById('location').addEventListener('change', function () {
           
        });

        // Inisialisasi canvas untuk tanda tangan
        const canvas = document.getElementById('signatureCanvas');
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

        // Fungsi untuk mendapatkan koordinat sentuhan atau mouse
        function getCoordinates(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;

            let x, y;
            if (e.touches) {
                x = (e.touches[0].clientX - rect.left) * scaleX;
                y = (e.touches[0].clientY - rect.top) * scaleY;
            } else {
                x = (e.offsetX || e.layerX) * scaleX;
                y = (e.offsetY || e.layerY) * scaleY;
            }
            return [x, y];
        }

        function startDrawing(e) {
            e.preventDefault(); // Mencegah scrolling atau zoom saat menggambar
            isDrawing = true;
            [lastX, lastY] = getCoordinates(e);
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault(); // Mencegah scrolling atau zoom saat menggambar

            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000';

            const [x, y] = getCoordinates(e);

            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(x, y);
            ctx.stroke();

            [lastX, lastY] = [x, y];
        }

        function stopDrawing(e) {
            isDrawing = false;
            ctx.beginPath();
        }

        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function saveOfficerSignature() {
            try {
                const canvas = document.getElementById('signatureCanvas');
                const signatureData = canvas.toDataURL('image/png');
                const signatureInput = document.getElementById('submitterSignatureData');

                if (!signatureInput) {
                    console.error('Element officerSignatureData tidak ditemukan');
                    return;
                }

                signatureInput.value = signatureData;

                // Buat preview container
                const previewContainer = document.createElement('div');
                previewContainer.id = 'signaturePreview';
                previewContainer.innerHTML = `
                    <img src="${signatureData}" alt="Tanda tangan Officer" class="max-w-full h-auto">
                `;

                // Ganti canvas dengan preview
                const canvasContainer = canvas.parentElement;
                canvas.remove();
                canvasContainer.appendChild(previewContainer);

                // Sembunyikan tombol clear dan save
                document.getElementById('clearSignature').style.display = 'none';
                document.getElementById('saveSubmitterSignature').style.display = 'none';

                alert('Tanda tangan berhasil disimpan');
                console.log('Signature data saved:', signatureData.substring(0, 100) + '...');
            } catch (error) {
                console.error('Error saving signature:', error);
                alert('Terjadi kesalahan saat menyimpan tanda tangan');
            }
        }

        // Event listeners untuk mouse
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        // Event listeners untuk sentuhan mobile
        canvas.addEventListener('touchstart', startDrawing, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDrawing);

        document.getElementById('clearSignature').addEventListener('click', clearCanvas);
        document.getElementById('saveSubmitterSignature').addEventListener('click', saveOfficerSignature);

        // Fungsi untuk mengecek status checkbox dan mengupdate radio button
        function updateRadioResult() {
            // Ambil semua checkbox berdasarkan ID yang ada
            const test1Checkboxes = [
                document.getElementById('test1_in_depan'),
                document.getElementById('test1_out_depan')
            ];
            const test2Checkboxes = [
                document.getElementById('test2_in_depan'),
                document.getElementById('test2_out_depan')
            ];
            const test3Checkboxes = [
                document.getElementById('test3_in_belakang'),
                document.getElementById('test3_out_belakang')
            ];
            const test4Checkboxes = [
                document.getElementById('test4_in_depan'),
                document.getElementById('test4_out_depan')
            ];

            // Cek apakah semua checkbox tercentang
            const allChecked = [...test1Checkboxes, ...test2Checkboxes, ...test3Checkboxes, ...test4Checkboxes]
                .every(checkbox => checkbox.checked);

            // Update radio button dan hidden input
            const resultPass = document.getElementById('resultPass');
            const resultFail = document.getElementById('resultFail');
            const resultHidden = document.getElementById('result');

            if (allChecked) {
                resultPass.checked = true;
                resultHidden.value = 'pass';
            } else {
                resultFail.checked = true;
                resultHidden.value = 'fail';
            }
            console.log('Checkbox status updated:', {
                allChecked,
                resultPass: resultPass.checked,
                resultFail: resultFail.checked,
                resultValue: resultHidden.value
            });
        }

        // Tambahkan event listener untuk semua checkbox
        const allCheckboxIds = [
            'test1_in_depan', 'test1_out_depan',
            'test2_in_depan', 'test2_out_depan',
            'test3_in_belakang', 'test3_out_belakang',
            'test4_in_depan', 'test4_out_depan'
        ];

        allCheckboxIds.forEach(id => {
            document.getElementById(id).addEventListener('change', updateRadioResult);
        });

        // Nonaktifkan radio button agar tidak bisa diklik manual
        document.getElementById('resultPass').addEventListener('click', (e) => e.preventDefault());
        document.getElementById('resultFail').addEventListener('click', (e) => e.preventDefault());

        // Inisialisasi status awal
        updateRadioResult();
    });
</script>

@if(!isset($isPdf))
    @endsection
@endif
