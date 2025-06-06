@if(!isset($isPdf))
    @extends('layouts.app')

    @section('content')
@endif


<div class="bg-white-100 px-4 sm:px-8 md:px-16 lg:px-32 xl:px-64">
    <div>
        <x-form-hhmd/>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Get the current date and time
        let now = new Date();

        // Format the date and time to match the input format (YYYY-MM-DDTHH:MM)
        let year = now.getFullYear();
        let month = (now.getMonth() + 1).toString().padStart(2, '0');
        let day = now.getDate().toString().padStart(2, '0');
        let hours = now.getHours().toString().padStart(2, '0');
        let minutes = now.getMinutes().toString().padStart(2, '0');

        let formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

        // Set the value of the input
        document.getElementById('testDateTime').value = formattedDateTime;

        // Validasi form dan tanda tangan
        const form = document.getElementById('hhmdForm');
        const submitButton = document.getElementById('submitButton');
        const buttonText = document.getElementById('buttonText');
        const buttonLoading = document.getElementById('buttonLoading');

        // Fungsi untuk mengecek lokasi
        async function checkLocation(location) {
            try {
                const response = await fetch(`/check-hhmd-location?location=${location}`);
                const data = await response.json();

                if (!data.available) {
                    Swal.fire({
                        title: 'Lokasi Tidak Tersedia',
                        text: data.message,
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                    document.getElementById('location').value = '';
                    return false;
                }
                return true;
            } catch (error) {
                console.error('Error checking location:', error);
                return false;
            }
        }

        // Event listener untuk form submit
        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            try {
                // Cek lokasi sebelum submit
                const location = document.getElementById('location').value;
                if (!(await checkLocation(location))) {
                    return false;
                }

                const signatureInput = document.getElementById('officerSignatureData');
                const supervisorSelect = document.getElementById('supervisor_id');

                // Validasi tanda tangan
                if (!signatureInput || !signatureInput.value) {
                    Swal.fire({
                        title: 'Tanda Tangan Diperlukan!',
                        text: 'Anda harus menyimpan tanda tangan terlebih dahulu sebelum submit form.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });

                    document.querySelector('.signature-section').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    return false;
                }

                // Validasi supervisor
                if (!supervisorSelect.value) {
                    Swal.fire({
                        title: 'Supervisor Diperlukan!',
                        text: 'Silakan pilih supervisor terlebih dahulu.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                    return false;
                }

                // Tampilkan loading state
                submitButton.disabled = true;
                buttonText.classList.add('hidden');
                buttonLoading.classList.remove('hidden');

                // Submit form dengan fetch
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    const data = await response.json();
                    throw new Error(data.message || 'Terjadi kesalahan saat submit form');
                }

                // Redirect akan ditangani oleh response
                window.location.href = response.url;

            } catch (error) {
                Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } finally {
                submitButton.disabled = false;
                buttonText.classList.remove('hidden');
                buttonLoading.classList.add('hidden');
            }
        });

        // Event listener untuk perubahan lokasi
        document.getElementById('location').addEventListener('change', function() {
            if (this.value) {
                checkLocation(this.value);
            }
        });

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
                const signatureInput = document.getElementById('officerSignatureData');

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
                document.getElementById('saveOfficerSignature').style.display = 'none';

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
        document.getElementById('saveOfficerSignature').addEventListener('click', saveOfficerSignature);

        // Fungsi untuk mengecek status checkbox dan mengupdate radio button
        function updateRadioResult() {
            const test2Checkbox = document.getElementById('test2');
            const resultPass = document.getElementById('resultPass');
            const resultFail = document.getElementById('resultFail');
            const resultHidden = document.getElementById('result');

            if (test2Checkbox.checked) {
                resultPass.checked = true;
                resultHidden.value = 'pass';
            } else {
                resultFail.checked = true;
                resultHidden.value = 'fail';
            }
        }

        // Event listener untuk checkbox
        document.getElementById('test2').addEventListener('change', updateRadioResult);

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
