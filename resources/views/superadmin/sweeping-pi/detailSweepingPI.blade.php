@extends('layouts.app')

@section('title', 'Logbook Sweeping Prohibited Items')

@section('content')
<div class="container mx-auto p-2 sm:p-4 max-w-full overflow-x-auto mt-16 sm:mt-20">
    <!-- Header -->
    <div class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl p-3 sm:p-6 mb-4 sm:mb-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 sm:gap-4">
            <div class="w-full">
                <div class="flex items-center mb-3 sm:mb-4">
                    <button onclick="history.back()" class="text-blue-600 hover:text-blue-800 mr-3 sm:mr-4 p-1">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </button>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-lg sm:text-2xl font-bold text-gray-900 truncate">Checklist Prohibited Items</h1>
                        <p class="text-sm sm:text-base text-gray-600 truncate">Tenant: <span class="font-semibold text-blue-600" id="tenant-name">{{ $tenant->tenant_name }}</span></p>
                        <p class="text-xs sm:text-sm text-gray-500">ID: {{ $logbook->sweepingpiID }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Summary - Mobile Optimized -->
        <div class="mt-4 sm:mt-6 grid grid-cols-3 gap-2 sm:gap-4">
            <div class="bg-blue-50 p-3 sm:p-4 rounded-lg sm:rounded-xl text-center">
                <div class="text-xl sm:text-2xl font-bold text-blue-600" id="completion-rate">0%</div>
                <div class="text-xs sm:text-sm text-blue-800">Selesai</div>
            </div>
            <div class="bg-green-50 p-3 sm:p-4 rounded-lg sm:rounded-xl text-center">
                <div class="text-xl sm:text-2xl font-bold text-green-600" id="total-checked">0</div>
                <div class="text-xs sm:text-sm text-green-800">Ceklist</div>
            </div>
            <div class="bg-yellow-50 p-3 sm:p-4 rounded-lg sm:rounded-xl text-center">
                <div class="text-xl sm:text-2xl font-bold text-yellow-600" id="total-pending">0</div>
                <div class="text-xs sm:text-sm text-yellow-800">Pending</div>
            </div>
        </div>
    </div>

    <!-- Checklist Table -->
    <div class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl overflow-hidden">
        <!-- Table Header with Month/Year -->
        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 text-white p-3 sm:p-4">
            <h3 class="text-lg sm:text-xl font-bold text-center" id="current-month-year"></h3>
        </div>

        <!-- Mobile View Toggle -->
        <div class="block sm:hidden bg-gray-50 p-3 border-b">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">Mode Tampilan:</span>
                <div class="flex bg-white rounded-lg p-1 shadow-sm">
                    <button onclick="toggleMobileView('table')" id="btn-table" class="mobile-view-btn active px-3 py-1 text-xs font-medium rounded-md transition-colors">
                        Tabel
                    </button>
                    <button onclick="toggleMobileView('cards')" id="btn-cards" class="mobile-view-btn px-3 py-1 text-xs font-medium rounded-md transition-colors">
                        Kartu
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div id="table-view" class="overflow-x-auto">
            <table class="min-w-full checklist-table border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="sticky left-0 bg-gray-100 px-2 sm:px-4 py-2 sm:py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 border-r-2 border-gray-300 z-20 item-header-cell">
                            PROHIBITED ITEMS
                        </th>
                        <!-- Days headers will be added here by JavaScript -->
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="checklist-tbody">
                    <!-- Checklist items will be populated by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div id="cards-view" class="hidden p-3 sm:p-4 space-y-3" style="display: none;">
            <div id="cards-container">
                <!-- Cards will be populated by JavaScript -->
            </div>
        </div>

        <!-- Note Modal -->
        <div id="note-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md mx-auto mt-20 sm:mt-40 p-4 sm:p-6 max-h-[80vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-base sm:text-lg font-semibold">Catatan</h2>
                    <span class="text-xs sm:text-sm text-gray-500" id="modal-info"></span>
                </div>
                <textarea id="note-textarea" rows="4" class="w-full border rounded p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Masukkan catatan untuk hari ini..."></textarea>
                <div class="mt-4 flex justify-end space-x-2">
                    <button onclick="closeNoteModal()" class="px-3 sm:px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 transition-colors text-sm">Batal</button>
                    <button onclick="saveNote()" class="px-3 sm:px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition-colors text-sm">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend - Mobile Optimized -->
    <div class="mt-4 sm:mt-6 bg-white rounded-lg sm:rounded-xl shadow-lg p-4 sm:p-6">
        <h4 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Keterangan:</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 text-xs sm:text-sm">
            <div class="flex items-center">
                <div class="w-5 h-5 sm:w-6 sm:h-6 bg-green-500 rounded mr-2 sm:mr-3 flex-shrink-0"></div>
                <span class="leading-tight">Sudah dicek hari ini</span>
            </div>
            <div class="flex items-center">
                <div class="w-5 h-5 sm:w-6 sm:h-6 bg-gray-300 rounded mr-2 sm:mr-3 flex-shrink-0"></div>
                <span class="leading-tight">Belum dicek</span>
            </div>
            <div class="flex items-center">
                <div class="w-5 h-5 sm:w-6 sm:h-6 bg-yellow-500 rounded mr-2 sm:mr-3 flex-shrink-0"></div>
                <span class="leading-tight">Terlewat (hari sebelumnya)</span>
            </div>
            <div class="flex items-center">
                <svg class="bg-[#3b82f6] w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 p-1 flex-shrink-0" fill="none" stroke="#ffffff" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span class="leading-tight">Ada catatan harian</span>
            </div>
            <div class="flex items-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span class="leading-tight">Tombol catatan harian</span>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-4 sm:p-6 flex items-center space-x-3 max-w-xs">
                <div class="animate-spin rounded-full h-5 w-5 sm:h-6 sm:w-6 border-b-2 border-blue-600"></div>
                <span class="text-sm sm:text-base text-gray-700">Menyimpan data...</span>
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
        width: 45px;
        min-width: 45px;
        padding: 6px 2px;
        text-align: center;
        border-right: 1px solid #e5e7eb;
        background-color: white;
        vertical-align: middle;
    }

    @media (min-width: 640px) {
        .checklist-cell {
            width: 60px;
            min-width: 60px;
            padding: 8px 4px;
        }
    }

    .item-name-cell {
        min-width: 180px;
        width: 180px;
        padding: 8px 12px;
        word-wrap: break-word;
        white-space: normal;
        line-height: 1.3;
        vertical-align: middle;
        font-size: 12px;
    }

    @media (min-width: 640px) {
        .item-name-cell {
            min-width: 250px;
            width: 250px;
            padding: 12px 16px;
            font-size: 14px;
            line-height: 1.4;
        }
    }

    .item-header-cell {
        min-width: 180px;
        width: 180px;
        font-size: 11px;
    }

    @media (min-width: 640px) {
        .item-header-cell {
            min-width: 250px;
            width: 250px;
            font-size: 14px;
        }
    }

    .note-button {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        border: 1px solid #d1d5db;
        background: white;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        font-size: 10px;
    }

    @media (min-width: 640px) {
        .note-button {
            width: 20px;
            height: 20px;
            font-size: 12px;
        }
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

    /* Mobile view buttons */
    .mobile-view-btn {
        color: #6b7280;
        background: transparent;
    }

    .mobile-view-btn.active {
        background: #3b82f6;
        color: white;
    }

    /* Mobile Card Styles */
    .item-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        background: white;
    }

    .item-card h4 {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        line-height: 1.3;
    }

    .days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        margin-bottom: 12px;
    }

    .day-cell {
        text-align: center;
        padding: 4px;
        border-radius: 4px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        min-height: 32px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .day-number {
        font-size: 10px;
        color: #6b7280;
        margin-bottom: 2px;
    }

    /* Checkbox styles for mobile */
    .mobile-checkbox {
        width: 20px;
        height: 20px;
        border: 2px solid;
        border-radius: 4px;
        transition: all 0.2s;
        cursor: pointer;
        position: relative;
    }

    .mobile-checkbox.checked {
        border-color: #10b981;
        background-color: #10b981;
    }

    .mobile-checkbox.unchecked {
        border-color: #d1d5db;
        background-color: white;
    }

    .mobile-checkbox.missed {
        border-color: #f59e0b;
        background-color: #fef3c7;
        cursor: not-allowed;
        opacity: 0.7;
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
        gap: 3px;
    }

    @media (min-width: 640px) {
        .cell-content {
            gap: 4px;
        }
    }

    /* Hide scrollbar on mobile for table */
    @media (max-width: 639px) {
        .overflow-x-auto::-webkit-scrollbar {
            height: 3px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    }

    /* Responsive improvements */
    @media (max-width: 639px) {
        .checklist-table th {
            font-size: 10px;
            padding: 6px 2px;
        }
        
        .checklist-table td {
            font-size: 11px;
        }
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
    let currentMobileView = 'table'; // 'table' or 'cards'

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
        renderCards();

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

    function toggleMobileView(view) {
        currentMobileView = view;
        
        // Update button styles
        document.getElementById('btn-table').classList.toggle('active', view === 'table');
        document.getElementById('btn-cards').classList.toggle('active', view === 'cards');
        
        // Show/hide views
        const tableView = document.getElementById('table-view');
        const cardsView = document.getElementById('cards-view');
        
        if (view === 'table') {
            tableView.style.display = 'block';
            cardsView.style.display = 'none';
        } else {
            tableView.style.display = 'none';
            cardsView.style.display = 'block';
            cardsView.classList.remove('hidden');
        }
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
            th.className = 'checklist-cell px-1 sm:px-2 py-2 sm:py-3 text-center text-xs sm:text-sm font-semibold text-gray-700 border-r border-gray-200 bg-gray-100';
            th.textContent = day;
            th.style.width = window.innerWidth < 640 ? '45px' : '60px';
            th.style.minWidth = window.innerWidth < 640 ? '45px' : '60px';
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
                <td class="sticky left-0 bg-white item-name-cell text-xs sm:text-sm font-medium text-gray-900 border-r-2 border-gray-300 z-10">
                    <div class="leading-tight">
                        ${item.items_name}
                        <div class="text-xs text-gray-500 mt-1">(${item.quantity} item)</div>
                    </div>
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
                    checkboxClass = 'border-yellow-500 bg-yellow-100 opacity-60';
                } else {
                    checkboxClass = 'border-gray-300 bg-white hover:border-blue-400';
                }

                const checkboxSize = window.innerWidth < 640 ? 'w-5 h-5' : 'w-6 h-6';
                const iconSize = window.innerWidth < 640 ? 'w-3 h-3' : 'w-4 h-4';

                tbodyHtml += `
                <td class="checklist-cell">
                    <div class="cell-content">
                        <button
                            type="button"
                            class="${checkboxSize} border-2 rounded transition-all duration-200 relative ${checkboxClass}"
                            onclick="${`toggleCheck(${itemIndex}, ${day})`}"
                            title="${isMissed ? `Terlewat - ${item.items_name} tanggal ${day + 1}` : `Toggle checklist ${item.items_name} tanggal ${day + 1}`}">
                        
                            ${isChecked ? `
                            <svg class="${iconSize} text-white absolute inset-0 m-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            ` : ''}
                        </button>
                        
                        ${itemIndex === prohibitedItems.length - 1 ? `
                        <button
                            type="button"
                            class="note-button mt-1 sm:mt-2 ${hasNote ? 'has-note' : ''}"
                            onclick="openNoteModal(${day})"
                            title="Tambah catatan untuk tanggal ${day + 1}${hasNote ? ' - Ada catatan' : ''}">
                            <svg class="w-2 h-2 sm:w-3 sm:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    function renderCards() {
        const container = document.getElementById('cards-container');
        if (!container) return;

        const daysInMonth = getDaysInMonth();
        let cardsHtml = '';

        prohibitedItems.forEach((item, itemIndex) => {
            cardsHtml += `
                <div class="item-card">
                    <h4>${item.items_name}</h4>
                    <p class="text-xs text-gray-500 mb-3">${item.quantity} item</p>
                    
                    <div class="days-grid">
            `;

            for (let day = 0; day < daysInMonth; day++) {
                const isChecked = checklist[itemIndex] && checklist[itemIndex][day];
                const hasNote = dailyNotes[day] && dailyNotes[day].trim() !== '';

                // Calculate isMissed
                const checkDate = new Date(currentYear, currentMonth - 1, day + 1);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                checkDate.setHours(0, 0, 0, 0);
                const isMissed = checkDate < today && !isChecked;

                let checkboxClass;
                if (isChecked) {
                    checkboxClass = 'checked';
                } else if (isMissed) {
                    checkboxClass = 'missed';
                } else {
                    checkboxClass = 'unchecked';
                }

                cardsHtml += `
                    <div class="day-cell">
                        <div class="day-number">${day + 1}</div>
                        <button
                            type="button"
                            class="mobile-checkbox ${checkboxClass}"
                            ${isMissed ? 'disabled' : ''}
                            onclick="${isMissed ? '' : `toggleCheck(${itemIndex}, ${day})`}"
                            title="${isMissed ? `Terlewat - ${item.items_name} tanggal ${day + 1}` : `Toggle checklist ${item.items_name} tanggal ${day + 1}`}">
                            ${isChecked ? `
                            <svg class="w-3 h-3 text-white absolute inset-0 m-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            ` : ''}
                        </button>
                    </div>
                `;
            }

            cardsHtml += `
                    </div>
            `;

            // Add notes section for the last item
            if (itemIndex === prohibitedItems.length - 1) {
                cardsHtml += `
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <h5 class="text-sm font-semibold text-gray-700 mb-2">Catatan Harian:</h5>
                        <div class="grid grid-cols-7 gap-2">
                `;

                for (let day = 0; day < daysInMonth; day++) {
                    const hasNote = dailyNotes[day] && dailyNotes[day].trim() !== '';
                    
                    cardsHtml += `
                        <button
                            type="button"
                            class="w-8 h-8 rounded border ${hasNote ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-600 border-gray-300'} text-xs font-medium hover:bg-blue-100 transition-colors"
                            onclick="openNoteModal(${day})"
                            title="Catatan tanggal ${day + 1}${hasNote ? ' - Ada catatan' : ''}">
                            ${day + 1}
                        </button>
                    `;
                }

                cardsHtml += `
                        </div>
                    </div>
                `;
            }

            cardsHtml += '</div>';
        });

        container.innerHTML = cardsHtml;
    }

    function toggleCheck(itemIndex, dayIndex) {
        if (!checklist[itemIndex]) {
            checklist[itemIndex] = {};
        }

        checklist[itemIndex][dayIndex] = !checklist[itemIndex][dayIndex];
        updateStats();
        renderTable(); // Re-render to update checkbox styles
        renderCards(); // Re-render cards view as well
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
        renderCards(); // Re-render cards view as well
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
        notification.className = `fixed top-4 right-4 p-3 sm:p-4 rounded-lg shadow-lg z-50 text-white text-sm sm:text-base max-w-xs sm:max-w-sm ${
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

    // Handle window resize
    window.addEventListener('resize', function() {
        // Re-render table to adjust cell sizes
        renderTable();
    });

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