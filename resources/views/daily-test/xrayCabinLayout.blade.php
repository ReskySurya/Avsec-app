@if(!isset($isPdf))
    @extends('layouts.app')

    @section('content')
@endif


<div class="bg-white-100 sm:px-2 md:px-16 lg:px-96 lg:mt-20">
    <div>
        <x-form-xray type="xrayCabin" :xrayCabinLocations="$xrayCabinLocations"/>
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
        let year = now.getFullYear();
        let month = (now.getMonth() + 1).toString().padStart(2, '0');
        let day = now.getDate().toString().padStart(2, '0');
        let hours = now.getHours().toString().padStart(2, '0');
        let minutes = now.getMinutes().toString().padStart(2, '0');

        let formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
        document.getElementById('testDateTime').value = formattedDateTime;

        // Validasi form dan tanda tangan
        const form = document.getElementById('xrayForm');
        const submitButton = document.getElementById('submitButton');
        const buttonText = document.getElementById('buttonText');
        const buttonLoading = document.getElementById('buttonLoading');

        // Fungsi untuk mengecek lokasi
        async function checkLocation(locationId) {
            try {
                const response = await fetch('/daily-test/xraycabin/check-location', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ location_id: locationId })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan saat memeriksa lokasi');
                }

                return data;
            } catch (error) {
                console.error('Error checking location:', error);
                throw error;
            }
        }

        // Event listener untuk form submission
        document.getElementById('xrayForm').addEventListener('submit', async function (e) {
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
                // Validasi lokasi dengan API
                await checkLocation(locationValue);

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

                const response = await fetch('/daily-test/xraycabin/store', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();

                console.log('Form submission result:', formData, result);
                if (response.ok) {
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
            if (this.value) {
                checkLocation(this.value);
            }
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
            const test2Checkboxes = [
                document.getElementById('test2aab'),
                document.getElementById('test2bab'),
                document.getElementById('test2ab'),
                document.getElementById('test2bb')
            ];
            const test3Checkboxes = [
                document.getElementById('test3ab_14'),
                document.getElementById('test3ab_16'),
                document.getElementById('test3ab_18'),
                document.getElementById('test3ab_20'),
                document.getElementById('test3ab_22'),
                document.getElementById('test3ab_24'),
                document.getElementById('test3b_14'),
                document.getElementById('test3b_16'),
                document.getElementById('test3b_18'),
                document.getElementById('test3b_20'),
                document.getElementById('test3b_22'),
                document.getElementById('test3b_24')
            ];
            const test1Checkboxes = [
                document.getElementById('test1aab_32'),
                document.getElementById('test1aab_30'),
                document.getElementById('test1aab_24'),
                document.getElementById('test1bab_30_1'),
                document.getElementById('test1bab_24_1'),
                document.getElementById('test1bab_30_2'),
                document.getElementById('test1bab_24_2'),
                document.getElementById('test1bab_24_3'),
                document.getElementById('test1ab_32'),
                document.getElementById('test1ab_30'),
                document.getElementById('test1ab_24'),
                document.getElementById('test1bb_30_1'),
                document.getElementById('test1bb_24_1'),
                document.getElementById('test1bb_30_2'),
                document.getElementById('test1bb_24_2'),
                document.getElementById('test1bb_24_3')
            ];
            const test4Checkboxes = [
                document.getElementById('test4ab_h15mm'),
                document.getElementById('test4ab_v15mm'),
                document.getElementById('test4ab_h20mm'),
                document.getElementById('test4ab_v20mm'),
                document.getElementById('test4b_h15mm'),
                document.getElementById('test4b_v15mm'),
                document.getElementById('test4b_h20mm'),
                document.getElementById('test4b_v20mm')
            ];

            const test5Checkboxes = [
                document.getElementById('test5ab_05mm'),
                document.getElementById('test5b_05mm'),
                document.getElementById('test5ab_10mm'),
                document.getElementById('test5b_10mm')
            ];

            // Cek apakah semua checkbox tercentang
            const allChecked = [...test1Checkboxes, ...test2Checkboxes, ...test3Checkboxes, ...test4Checkboxes, ...test5Checkboxes]
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
            'test2aab',
            'test2bab',
            'test2ab',
            'test2bb',
            'test3ab_14',
            'test3ab_16',
            'test3ab_18',
            'test3ab_20',
            'test3ab_22',
            'test3ab_24',
            'test3b_14',
            'test3b_16',
            'test3b_18',
            'test3b_20',
            'test3b_22',
            'test3b_24',
            'test1aab_32',
            'test1aab_30',
            'test1aab_24',
            'test1bab_30_1',
            'test1bab_24_1',
            'test1bab_30_2',
            'test1bab_24_2',
            'test1bab_24_3',
            'test1ab_32',
            'test1ab_30',
            'test1ab_24',
            'test1bb_30_1',
            'test1bb_24_1',
            'test1bb_30_2',
            'test1bb_24_2',
            'test1bb_24_3',
            'test4ab_h15mm',
            'test4ab_v15mm',
            'test4ab_h20mm',
            'test4ab_v20mm',
            'test4b_h15mm',
            'test4b_v15mm',
            'test4b_h20mm',
            'test4b_v20mm',
            'test5ab_05mm',
            'test5b_05mm',
            'test5ab_10mm',
            'test5b_10mm'
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
