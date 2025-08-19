@extends('layouts.app')

@section('title', 'Logbook Sweeping Prohibited Items')

@section('content')
<div class="container mx-auto p-4 max-w-full overflow-x-auto mt-20">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <div class="flex items-center mb-4">
                    <button onclick="history.back()" class="text-blue-600 hover:text-blue-800 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Checklist Prohibited Items</h1>
                        <p class="text-gray-600">Tenant: <span class="font-semibold text-blue-600" id="tenant-name">{{ $tenant->tenant_name }}</span></p>
                        <p class="text-gray-500 text-sm">ID: {{ $logbook->sweepingpiID }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Summary -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 p-4 rounded-xl">
                <div class="text-2xl font-bold text-blue-600" id="completion-rate">0%</div>
                <div class="text-sm text-blue-800">Rating Selesai</div>
            </div>
            <div class="bg-green-50 p-4 rounded-xl">
                <div class="text-2xl font-bold text-green-600" id="total-checked">0</div>
                <div class="text-sm text-green-800">Total Ceklist</div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-xl">
                <div class="text-2xl font-bold text-yellow-600" id="total-pending">0</div>
                <div class="text-sm text-yellow-800">Pending</div>
            </div>
        </div>
    </div>

    <!-- Checklist Table -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Table Header with Month/Year -->
        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 text-white p-4">
            <h3 class="text-xl font-bold text-center" id="current-month-year"></h3>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table class="min-w-full checklist-table border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="sticky left-0 bg-gray-100 px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r-2 border-gray-300 z-20" style="min-width: 250px; width: 250px;">
                            PROHIBITED ITEMS
                        </th>
                        <!-- Days headers will be added here by JavaScript -->
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="checklist-tbody">
                    <!-- Checklist items will be populated by JavaScript -->
                </tbody>
            </table>

            <!-- Note Modal -->
            <div id="note-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
                <div class="bg-white rounded-xl shadow-xl max-w-md mx-auto mt-40 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Catatan</h2>
                        <span class="text-sm text-gray-500" id="modal-info"></span>
                    </div>
                    <textarea id="note-textarea" rows="4" class="w-full border rounded p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan catatan untuk hari ini..."></textarea>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button onclick="closeNoteModal()" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 transition-colors">Batal</button>
                        <button onclick="saveNote()" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition-colors">Simpan</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Legend -->
    <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Keterangan:</h4>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 text-sm">
            <div class="flex items-center">
                <div class="w-6 h-6 bg-green-500 rounded mr-3"></div>
                <span>Sudah dicek hari ini</span>
            </div>
            <div class="flex items-center">
                <div class="w-6 h-6 bg-gray-300 rounded mr-3"></div>
                <span>Belum dicek</span>
            </div>
            <div class="flex items-center">
                <div class="w-6 h-6 bg-yellow-500 rounded mr-3"></div>
                <span>Terlewat (hari sebelumnya)</span>
            </div>
            <div class="flex items-center">
                <svg class="bg-[#3b82f6] w-6 h-6 mr-3 p-1" fill="none" stroke="#ffffff" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Ada catatan harian</span>
            </div>
            <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Tombol catatan harian</span>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-700">Menyimpan data...</span>
            </div>
        </div>
    </div>
</div>

<style>
    .checklist-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .checklist-cell {
        width: 60px;
        min-width: 60px;
        padding: 8px 4px;
        text-align: center;
        border-right: 1px solid #e5e7eb;
        background-color: white;
        vertical-align: middle;
    }

    .item-name-cell {
        min-width: 250px;
        width: 250px;
        padding: 12px 16px;
        word-wrap: break-word;
        white-space: normal;
        line-height: 1.4;
        vertical-align: middle;
    }

    .note-button {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        border: 1px solid #d1d5db;
        background: white;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        font-size: 12px;
    }

    .note-button:hover {
        border-color: #3b82f6;
        color: #3b82f6;
        background: #eff6ff;
    }

    .note-button.has-note {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    /* Ensure sticky positioning works properly */
    .sticky {
        position: -webkit-sticky;
        position: sticky;
    }

    /* Fix for table borders */
    .checklist-table td {
        border-bottom: 1px solid #f3f4f6;
    }

    .checklist-table tr:hover td {
        background-color: #f9fafb;
    }

    .checklist-table tr:hover .sticky {
        background-color: #f9fafb;
    }

    .cell-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
</style>

<script>
    // Global variables
    let tenant = @json($tenant);
    let logbookId = '{{ $logbook->sweepingpiID }}';
    let currentMonth = '{{ $month }}';
    let currentYear = '{{ $year }}';
    let prohibitedItems = @json($prohibitedItems);
    let checklist = @json($checklistData ?? []);
    let dailyNotes = @json($dailyNotes ?? []); // Notes per hari saja

    let saveTimeout = null;
    let currentModalItem = null;
    let currentModalDay = null;

    let statistics = {
        completion_rate: 0,
        total_checked: 0,
        total_pending: 0,
        total_violations: 0
    };

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeApp();
    });

    function initializeApp() {
        console.log('=== DEBUG INFO ===');
        console.log('Prohibited Items:', prohibitedItems);
        console.log('Prohibited Items Length:', prohibitedItems?.length);
        console.log('Checklist Data:', checklist);
        console.log('Daily Notes:', dailyNotes);
        console.log('Current Month:', currentMonth);
        console.log('Current Year:', currentYear);
        console.log('Days in Month:', getDaysInMonth());

        // Check if data exists
        if (!prohibitedItems || prohibitedItems.length === 0) {
            console.error('❌ PROHIBITED ITEMS IS EMPTY OR NULL!');
            document.getElementById('checklist-tbody').innerHTML = `
            <tr>
                <td colspan="32" class="text-center py-8 text-gray-500">
                    tidak ada prohibited items yang ditemukan. Tolong cek kembali data.
                </td>
            </tr>
        `;
            return;
        }

        // Set month/year
        const months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        document.getElementById('current-month-year').textContent = `${months[currentMonth - 1]} ${currentYear}`;

        // Initialize checklist and notes
        initializeChecklist();
        initializeNotes();

        // Render table
        renderTable();

        // Update statistics
        updateStats();
    }

    function getDaysInMonth() {
        return new Date(currentYear, currentMonth, 0).getDate();
    }

    function initializeChecklist() {
        if (!prohibitedItems || prohibitedItems.length === 0) {
            console.error('No prohibited items found!');
            return;
        }

        const daysInMonth = getDaysInMonth();

        prohibitedItems.forEach((item, itemIndex) => {
            if (!checklist[itemIndex]) {
                checklist[itemIndex] = {};
            }

            for (let day = 0; day < daysInMonth; day++) {
                if (checklist[itemIndex][day] === undefined) {
                    checklist[itemIndex][day] = false;
                }
            }
        });

        console.log('Checklist initialized:', checklist);
    }

    function initializeNotes() {
        console.log('🔄 Initializing notes...');
        console.log('📝 dailyNotes from server:', dailyNotes);

        if (!dailyNotes) {
            console.log('📝 dailyNotes is null/undefined, creating empty object');
            dailyNotes = {};
        }

        const daysInMonth = getDaysInMonth();

        for (let day = 0; day < daysInMonth; day++) {
            if (dailyNotes[day] === undefined) {
                dailyNotes[day] = '';
            } else if (dailyNotes[day]) {
                console.log(`📝 Found existing note for day ${day + 1}:`, dailyNotes[day]);
            }
        }

        console.log('📝 Daily notes after initialization:', dailyNotes);
    }

    function renderTable() {
        console.log('🔄 Starting renderTable...');

        const daysInMonth = getDaysInMonth();
        console.log('Days in month:', daysInMonth);

        // First, add day headers to the existing header row
        const headerRow = document.querySelector('thead tr');
        if (!headerRow) {
            console.error('❌ Header row not found!');
            return;
        }

        // Remove existing day headers (keep only the first th)
        while (headerRow.children.length > 1) {
            headerRow.removeChild(headerRow.lastChild);
        }

        // Add day headers
        for (let day = 1; day <= daysInMonth; day++) {
            const th = document.createElement('th');
            th.className = 'checklist-cell px-2 py-3 text-center text-sm font-semibold text-gray-700 border-r border-gray-200 bg-gray-100';
            th.textContent = day;
            th.style.width = '60px';
            th.style.minWidth = '60px';
            headerRow.appendChild(th);
        }

        // Render checklist items
        const tbody = document.getElementById('checklist-tbody');
        if (!tbody) {
            console.error('❌ Tbody not found!');
            return;
        }

        let tbodyHtml = '';

        console.log('📝 Rendering', prohibitedItems.length, 'items...');

        prohibitedItems.forEach((item, itemIndex) => {
            console.log(`Rendering item ${itemIndex}:`, item.items_name);

            tbodyHtml += `
            <tr class="hover:bg-gray-50 transition-colors duration-150">
                <td class="sticky left-0 bg-white item-name-cell text-sm font-medium text-gray-900 border-r-2 border-gray-300 z-10">
                    ${item.items_name}
                    <span class="text-sm text-gray-500">(${item.quantity} item)</span>
                </td>
        `;

            for (let day = 0; day < daysInMonth; day++) {
                const isChecked = checklist[itemIndex] && checklist[itemIndex][day];
                const hasNote = dailyNotes[day] && dailyNotes[day].trim() !== '';

                // Calculate isMissed for this specific day and item
                const checkDate = new Date(currentYear, currentMonth - 1, day + 1);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                checkDate.setHours(0, 0, 0, 0);
                const isMissed = checkDate < today && !isChecked;

                // Get checkbox class
                let checkboxClass;
                if (isChecked) {
                    checkboxClass = 'border-green-500 bg-green-500';
                } else if (isMissed) {
                    checkboxClass = 'border-yellow-500 bg-yellow-100 cursor-not-allowed opacity-60';
                } else {
                    checkboxClass = 'border-gray-300 bg-white hover:border-blue-400';
                }

                tbodyHtml += `
                <td class="checklist-cell">
                    <div class="cell-content">
                        <button
                            type="button"
                            class="w-6 h-6 border-2 rounded transition-all duration-200 ${!isMissed ? 'hover:scale-110' : ''} relative ${checkboxClass}"
                            ${isMissed ? 'disabled' : ''}
                            onclick="${isMissed ? '' : `toggleCheck(${itemIndex}, ${day})`}"
                            title="${isMissed ? `Terlewat - ${item.items_name} tanggal ${day + 1}` : `Toggle checklist ${item.items_name} tanggal ${day + 1}`}">
                        
                            ${isChecked ? `
                            <svg class="w-4 h-4 text-white absolute inset-0 m-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            ` : ''}
                        </button>
                        
                        ${itemIndex === prohibitedItems.length - 1 ? `
                        <button
                            type="button"
                            class="note-button mt-2 ${hasNote ? 'has-note' : ''}"
                            onclick="openNoteModal(${day})"
                            title="Tambah catatan untuk tanggal ${day + 1}${hasNote ? ' - Ada catatan' : ''}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        ` : ''
                        }
                    </div>
                </td>
            `;
            }

            tbodyHtml += '</tr>';
        });

        console.log('📄 Generated HTML length:', tbodyHtml.length);
        tbody.innerHTML = tbodyHtml;
        console.log('✅ Table rendered successfully');
    }

    function toggleCheck(itemIndex, dayIndex) {
        if (!checklist[itemIndex]) {
            checklist[itemIndex] = {};
        }

        checklist[itemIndex][dayIndex] = !checklist[itemIndex][dayIndex];
        updateStats();
        renderTable(); // Re-render to update checkbox styles
        autoSave();
    }

    function openNoteModal(dayIndex) {
        console.log(`🔄 Opening note modal for day ${dayIndex + 1}`);
        console.log(`📝 Existing note:`, dailyNotes[dayIndex]);

        currentModalDay = dayIndex;
        currentModalItem = null;

        const dayNumber = dayIndex + 1;

        document.getElementById('modal-info').textContent = `Catatan Tanggal ${dayNumber}`;
        document.getElementById('note-textarea').value = dailyNotes[dayIndex] ? dailyNotes[dayIndex] : '';
        document.getElementById('note-modal').classList.remove('hidden');

        // Focus on textarea
        setTimeout(() => {
            document.getElementById('note-textarea').focus();
        }, 100);
    }


    function closeNoteModal() {
        document.getElementById('note-modal').classList.add('hidden');
        currentModalItem = null;
        currentModalDay = null;
    }

    function saveNote() {
        if (currentModalDay === null) return;

        const noteText = document.getElementById('note-textarea').value;
        dailyNotes[currentModalDay] = noteText;

        closeNoteModal();
        renderTable(); // Re-render to update note button styles
        autoSave();

        showNotification('Catatan berhasil disimpan', 'success');
    }

    function updateStats() {
        let totalChecked = 0;
        const daysInMonth = getDaysInMonth();
        const totalCells = prohibitedItems.length * daysInMonth;

        prohibitedItems.forEach((item, itemIndex) => {
            for (let day = 0; day < daysInMonth; day++) {
                if (checklist[itemIndex] && checklist[itemIndex][day]) {
                    totalChecked++;
                }
            }
        });

        statistics.completion_rate = totalCells > 0 ? Math.round((totalChecked / totalCells) * 100) : 0;
        statistics.total_checked = totalChecked;
        statistics.total_pending = totalCells - totalChecked;

        // Update DOM
        document.getElementById('completion-rate').textContent = statistics.completion_rate + '%';
        document.getElementById('total-checked').textContent = statistics.total_checked;
        document.getElementById('total-pending').textContent = statistics.total_pending;
    }

    function autoSave() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            saveProgress();
        }, 2000); // Auto save after 2 seconds of inactivity
    }

    async function saveProgress() {
        const loadingOverlay = document.getElementById('loading-overlay');

        try {
            loadingOverlay.classList.remove('hidden');
            console.log('Saving progress...');

            const saveUrl = `{{ route('logbookSweppingPI.store') }}`;

            const response = await fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    logbook_id: logbookId,
                    items_name: prohibitedItems.map(item => item.items_name),
                    quantity: prohibitedItems.map(item => item.quantity),
                    checklist_data: checklist,
                    daily_notes: formatDailyNotesForBackend(dailyNotes), // Fungsi baru
                })
            });

            const data = await response.json();
            if (data.success) {
                console.log('Data saved successfully');
                showNotification('Data berhasil disimpan', 'success');
            } else {
                console.error('Error: ' + data.message);
                showNotification('Error: ' + data.message, 'error');
            }
        } catch (error) {
            console.error('Error saving progress:', error);
            showNotification('Terjadi kesalahan saat menyimpan data', 'error');
        } finally {
            loadingOverlay.classList.add('hidden');
        }
    }

    function showNotification(message, type = 'info') {
        // Simple notification - you can replace with a proper notification library
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 text-white ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);

        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Escape key to close modal
        if (e.key === 'Escape') {
            closeNoteModal();
        }
        // Ctrl+S to save
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            saveProgress();
        }
        // Enter key to save note when modal is open
        if (e.key === 'Enter' && e.ctrlKey && currentModalDay !== null) {
            e.preventDefault();
            saveNote();
        }
    });

   function formatDailyNotesForBackend(dailyNotes) {
    console.log('🐛 formatDailyNotesForBackend input:', dailyNotes);
    console.log('🐛 currentYear:', currentYear, 'currentMonth:', currentMonth);
    
    const formattedNotes = [];
    const daysInMonth = getDaysInMonth();

    for (let day = 0; day < daysInMonth; day++) {
        if (dailyNotes[day] && dailyNotes[day].trim() !== '') {
            // PERBAIKAN: Gunakan format tanggal yang lebih explicit
            const dayNumber = day + 1; // Convert 0-based to 1-based
            const dateString = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(dayNumber).padStart(2, '0')}`;
            
            const noteItem = {
                tanggal: dateString,
                notes: dailyNotes[day]
            };

            console.log(`🐛 Day ${dayNumber} (index ${day}) - Adding note:`, noteItem);
            formattedNotes.push(noteItem);
        }
    }

    console.log('🐛 formatDailyNotesForBackend output:', formattedNotes);
    return formattedNotes;
}
</script>
@endsection