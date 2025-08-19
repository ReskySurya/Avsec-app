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

            <!-- <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="saveProgress()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan
                </button>
            </div> -->
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
            <!-- <div class="bg-red-50 p-4 rounded-xl">
                <div class="text-2xl font-bold text-red-600" id="total-violations">0</div>
                <div class="text-sm text-red-800">Violations</div>
            </div> -->
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

            <!-- Notes Section -->
            <div class="border-t border-gray-200 bg-white">
                <div class="flex items-stretch">
                    <div class="sticky left-0 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-700 border-r-2 border-gray-300 z-10 flex items-center" style="min-width: 250px; width: 250px;">
                        CATATAN:
                    </div>
                    <div class="flex-1 p-2">
                        <input
                            type="text"
                            id="notes-input"
                            class="w-full h-8 text-sm border border-gray-300 rounded px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Tambahkan catatan untuk bulan ini..."
                            value="{{ $logbook->notes ?? '' }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Keterangan:</h4>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-500 rounded mr-3"></div>
                <span>Sudah dicek hari ini</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-gray-300 rounded mr-3"></div>
                <span>Belum dicek</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-yellow-500 rounded mr-3"></div>
                <span>Terlewat (hari sebelumnya)</span>
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
        width: 45px;
        min-width: 45px;
        padding: 8px 4px;
        text-align: center;
        border-right: 1px solid #e5e7eb;
        background-color: white;
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
</style>

<script>
    // Global variables
    let tenant = @json($tenant);
    let logbookId = '{{ $logbook->sweepingpiID }}';
    let currentMonth = '{{ $month }}';
    let currentYear = '{{ $year }}';
    let prohibitedItems = @json($prohibitedItems);
    let checklist = @json($checklistData ?? []);
    let notes = '{!! addslashes($logbook->notes ?? "") !!}';
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
        console.log('Current Month:', currentMonth);
        console.log('Current Year:', currentYear);
        console.log('Days in Month:', getDaysInMonth());

        // Check if data exists
        if (!prohibitedItems || prohibitedItems.length === 0) {
            console.error('❌ PROHIBITED ITEMS IS EMPTY OR NULL!');
            document.getElementById('checklist-tbody').innerHTML = `
            <tr>
                <td colspan="32" class="text-center py-8 text-gray-500">
                    No prohibited items data found. Please check your data source.
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

        // Set notes
        document.getElementById('notes-input').value = notes;

        // Initialize checklist
        initializeChecklist();

        // Render table
        renderTable();

        // Update statistics
        updateStats();

        // Add notes change listener
        document.getElementById('notes-input').addEventListener('input', function() {
            notes = this.value;
            autoSave();
        });
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

    // function getCheckboxClass(itemIndex, dayIndex) {
    //     const isChecked = checklist[itemIndex] && checklist[itemIndex][dayIndex];
    //     const currentDate = new Date();
    //     const checkDate = new Date(currentYear, currentMonth - 1, dayIndex + 1);
    //     const today = new Date();
    //     today.setHours(0, 0, 0, 0);
    //     checkDate.setHours(0, 0, 0, 0);

    //     const isMissed = checkDate < today && !isChecked;

    //     if (isChecked) {
    //         return 'border-green-500 bg-green-500';
    //     } else if (isMissed) {
    //         return 'border-yellow-500 bg-yellow-100 cursor-not-allowed opacity-60';
    //     } else {
    //         return 'border-gray-300 bg-white hover:border-blue-400';
    //     }
    // }

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
            th.style.width = '45px';
            th.style.minWidth = '45px';
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
                    <div class="flex justify-center items-center h-full">
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
        // document.getElementById('total-violations').textContent = statistics.total_violations;
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

            const saveUrl = `{{ route('logbookSweppingPI.store') }}`; // Adjust route name as needed

            const response = await fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    logbook_id: logbookId,
                    items_name: prohibitedItems.map(item => item.items_name),
                    checklist_data: checklist,
                    notes: notes
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
            closeModal();
        }
        // Ctrl+S to save
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            saveProgress();
        }
    });
</script>
@endsection