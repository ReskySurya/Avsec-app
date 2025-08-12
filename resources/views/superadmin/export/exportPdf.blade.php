@extends('layouts.app')

@section('title', 'Logbook Pos Jaga ')
@section('content')
{{-- Document Selector Component --}}
<div class="mb-8">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Daily Test Card --}}
        <div class="document-card group border-2 border-gray-200 rounded-lg p-5 cursor-pointer transition-all duration-300 hover:border-blue-500 hover:-translate-y-1 hover:shadow-lg bg-white"
             onclick="toggleDocument(this, 'daily-test')"
             data-type="daily-test">
            <div class="text-center">
                <span class="text-3xl mb-3 block">📝</span>
                <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">
                    Daily Test
                </h3>
                <p class="text-sm text-gray-600 group-hover:text-gray-700">
                    Dokumen hasil tes harian
                </p>
            </div>
        </div>

        {{-- Logbook Card --}}
        <div class="document-card group border-2 border-gray-200 rounded-lg p-5 cursor-pointer transition-all duration-300 hover:border-blue-500 hover:-translate-y-1 hover:shadow-lg bg-white"
             onclick="toggleDocument(this, 'logbook')"
             data-type="logbook">
            <div class="text-center">
                <span class="text-3xl mb-3 block">📚</span>
                <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">
                    Logbook Harian
                </h3>
                <p class="text-sm text-gray-600 group-hover:text-gray-700">
                    Catatan aktivitas harian
                </p>
            </div>
        </div>

        {{-- Vehicle Checklist Card --}}
        <div class="document-card group border-2 border-gray-200 rounded-lg p-5 cursor-pointer transition-all duration-300 hover:border-blue-500 hover:-translate-y-1 hover:shadow-lg bg-white"
             onclick="toggleDocument(this, 'vehicle-checklist')"
             data-type="vehicle-checklist">
            <div class="text-center">
                <span class="text-3xl mb-3 block">🚗</span>
                <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">
                    Checklist Kendaraan
                </h3>
                <p class="text-sm text-gray-600 group-hover:text-gray-700">
                    Pemeriksaan kondisi kendaraan
                </p>
            </div>
        </div>

        {{-- Attendance Card --}}
        <div class="document-card group border-2 border-gray-200 rounded-lg p-5 cursor-pointer transition-all duration-300 hover:border-blue-500 hover:-translate-y-1 hover:shadow-lg bg-white"
             onclick="toggleDocument(this, 'attendance')"
             data-type="attendance">
            <div class="text-center">
                <span class="text-3xl mb-3 block">⏰</span>
                <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">
                    Dokumen Absensi
                </h3>
                <p class="text-sm text-gray-600 group-hover:text-gray-700">
                    Data kehadiran dan ketidakhadiran
                </p>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript untuk Toggle Function --}}
<script>
    let selectedDocuments = [];

    function toggleDocument(card, docType) {
        const index = selectedDocuments.indexOf(docType);

        if (index > -1) {
            // Remove selection
            selectedDocuments.splice(index, 1);
            card.classList.remove('border-blue-500', 'bg-gradient-to-br', 'from-blue-500', 'to-blue-600', 'text-white');
            card.classList.add('border-gray-200', 'bg-white');

            // Reset text colors
            const title = card.querySelector('h3');
            const desc = card.querySelector('p');
            title.classList.remove('text-white');
            title.classList.add('text-gray-800', 'group-hover:text-blue-600');
            desc.classList.remove('text-blue-100');
            desc.classList.add('text-gray-600', 'group-hover:text-gray-700');
        } else {
            // Add selection
            selectedDocuments.push(docType);
            card.classList.remove('border-gray-200', 'bg-white');
            card.classList.add('border-blue-500', 'bg-gradient-to-br', 'from-blue-500', 'to-blue-600', 'text-white');

            // Update text colors for selected state
            const title = card.querySelector('h3');
            const desc = card.querySelector('p');
            title.classList.remove('text-gray-800', 'group-hover:text-blue-600');
            title.classList.add('text-white');
            desc.classList.remove('text-gray-600', 'group-hover:text-gray-700');
            desc.classList.add('text-blue-100');
        }

        updateSelectedCount();
    }

    function updateSelectedCount() {
        const countElement = document.getElementById('selectedCount');
        const countText = document.getElementById('countText');

        if (selectedDocuments.length > 0) {
            countElement.classList.remove('hidden');
            countText.textContent = `${selectedDocuments.length} dokumen dipilih`;
        } else {
            countElement.classList.add('hidden');
        }
    }

    // Function to get selected documents (untuk digunakan di form submit)
    function getSelectedDocuments() {
        return selectedDocuments;
    }

    // Function to reset selections
    function resetDocumentSelection() {
        selectedDocuments = [];
        document.querySelectorAll('.document-card').forEach(card => {
            card.classList.remove('border-blue-500', 'bg-gradient-to-br', 'from-blue-500', 'to-blue-600', 'text-white');
            card.classList.add('border-gray-200', 'bg-white');

            const title = card.querySelector('h3');
            const desc = card.querySelector('p');
            title.classList.remove('text-white');
            title.classList.add('text-gray-800', 'group-hover:text-blue-600');
            desc.classList.remove('text-blue-100');
            desc.classList.add('text-gray-600', 'group-hover:text-gray-700');
        });
        updateSelectedCount();
    }
</script>

{{-- Custom CSS untuk mobile responsiveness --}}
<style>
    @media (max-width: 640px) {
        .document-card {
            padding: 1rem;
        }

        .document-card span {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .document-card h3 {
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .document-card p {
            font-size: 0.75rem;
        }
    }
</style>
@endsection
