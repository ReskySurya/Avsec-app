@extends('layouts.app')

@section('title', 'Lihat Dokumen: ' . $document->title)

@push('styles')
    {{-- CSS bawaan PDF.js untuk text layer, dll. --}}
    <link rel="stylesheet" href="{{ asset('pdfjs/web/viewer.css') }}">
    <style>
        /* Custom styles untuk mobile optimization */
        .pdf-toolbar {
            background: linear-gradient(135deg, #374151, #1f2937);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .toolbar-button {
            min-width: 44px; /* Minimum touch target size */
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .toolbar-button:active {
            transform: scale(0.95);
        }

        .zoom-controls {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .page-info {
            font-size: 14px;
            min-width: 120px;
            text-align: center;
            user-select: none;
        }

        #pdf-container {
            position: relative;
            overflow: hidden;
            touch-action: pan-x pan-y pinch-zoom;
        }

        #pdf-canvas {
            display: block;
            max-width: 100%;
            height: auto;
            cursor: grab;
            transition: transform 0.2s ease;
        }

        #pdf-canvas:active {
            cursor: grabbing;
        }

        .canvas-wrapper {
            position: relative;
            overflow: auto;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Touch-friendly scrollbars */
        .canvas-wrapper::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .canvas-wrapper::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
        }

        .canvas-wrapper::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.3);
            border-radius: 4px;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .container {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .page-info {
                font-size: 12px;
                min-width: 100px;
            }

            .toolbar-controls {
                gap: 0.5rem;
            }
        }

        /* Full screen mode */
        .fullscreen-mode {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1000;
            background: #1f2937;
        }

        .fullscreen-mode .pdf-toolbar {
            position: sticky;
            top: 0;
            z-index: 1001;
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8" id="main-container">
    {{-- Header Halaman --}}
    <div class="mb-4">
        <a href="{{ route('folders.show', $document->folder) }}" class="text-blue-600 hover:underline mb-4 inline-block flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Folder
        </a>
        <h1 class="text-lg sm:text-2xl font-bold text-gray-800 truncate">{{ $document->title }}</h1>
    </div>

    {{-- Toolbar Kontrol PDF yang Diperbaiki --}}
    <div class="pdf-toolbar text-white p-2 rounded-t-lg sticky top-0 z-10">
        <div class="flex items-center justify-between toolbar-controls">
            {{-- Navigation Controls --}}
            <div class="flex items-center gap-2">
                <button id="prev" class="toolbar-button px-2 py-2 bg-gray-700 rounded hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button id="next" class="toolbar-button px-2 py-2 bg-gray-700 rounded hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            {{-- Page Info --}}
            <div class="page-info">
                <span id="page_num">0</span> / <span id="page_count">0</span>
            </div>

            {{-- Zoom and Display Controls --}}
            <div class="flex items-center gap-2">
                <div class="zoom-controls">
                    <button id="zoom-out" class="toolbar-button w-8 h-8 bg-gray-700 rounded hover:bg-gray-600 text-sm">-</button>
                    <span id="zoom-level" class="text-xs">100%</span>
                    <button id="zoom-in" class="toolbar-button w-8 h-8 bg-gray-700 rounded hover:bg-gray-600 text-sm">+</button>
                </div>
                <button id="fit-width" class="toolbar-button px-2 py-1 bg-gray-700 rounded hover:bg-gray-600 text-xs hidden sm:block">Fit</button>
                <button id="fullscreen" class="toolbar-button p-1 bg-gray-700 rounded hover:bg-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4h4M4 16v4h4m8-16h4v4m-4 12h4v-4"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Container untuk Canvas PDF --}}
    <div id="pdf-container" class="bg-gray-100 shadow-inner" style="height: calc(100vh - 200px);">
        {{-- Tampilan Loading --}}
        <div id="loading-spinner" class="flex flex-col justify-center items-center h-full">
            <svg class="animate-spin h-10 w-10 text-gray-700 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 text-sm">Memuat dokumen...</p>
        </div>

        {{-- Canvas Wrapper untuk scrolling --}}
        <div class="canvas-wrapper hidden" id="canvas-wrapper">
            <canvas id="pdf-canvas"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('pdfjs/build/pdf.mjs') }}" type="module"></script>
<script type="module">
    const { pdfjsLib } = globalThis;
    pdfjsLib.GlobalWorkerOptions.workerSrc = `{{ asset('pdfjs/build/pdf.worker.mjs') }}`;

    // Ambil semua elemen
    const url = '{{ route('documents.view', $document) }}';
    const pdfContainer = document.getElementById('pdf-container');
    const canvas = document.getElementById('pdf-canvas');
    const canvasWrapper = document.getElementById('canvas-wrapper');
    const loadingSpinner = document.getElementById('loading-spinner');
    const prevButton = document.getElementById('prev');
    const nextButton = document.getElementById('next');
    const pageNumSpan = document.getElementById('page_num');
    const pageCountSpan = document.getElementById('page_count');
    const zoomInButton = document.getElementById('zoom-in');
    const zoomOutButton = document.getElementById('zoom-out');
    const fitWidthButton = document.getElementById('fit-width');
    const fullscreenButton = document.getElementById('fullscreen');
    const zoomLevelSpan = document.getElementById('zoom-level');
    const mainContainer = document.getElementById('main-container');
    const ctx = canvas.getContext('2d');

    let pdfDoc = null;
    let pageNum = 1;
    let scale = 1;
    let isFullscreen = false;

    // Touch and pan support
    let isDragging = false;
    let startX, startY, scrollLeft, scrollTop;

    // Fungsi untuk me-render halaman PDF
    function renderPage(num, customScale = null) {
        pdfDoc.getPage(num).then(page => {
            const viewport = page.getViewport({ scale: 1 });

            // Hitung scale yang tepat
            if (customScale === null) {
                const containerWidth = pdfContainer.clientWidth - 40; // padding
                const containerHeight = pdfContainer.clientHeight - 40;
                const scaleX = containerWidth / viewport.width;
                const scaleY = containerHeight / viewport.height;
                scale = Math.min(scaleX, scaleY, 2); // max zoom 200%
            } else {
                scale = customScale;
            }

            const scaledViewport = page.getViewport({ scale: scale });

            canvas.height = scaledViewport.height;
            canvas.width = scaledViewport.width;

            page.render({
                canvasContext: ctx,
                viewport: scaledViewport
            });

            // Update zoom level display
            zoomLevelSpan.textContent = Math.round(scale * 100) + '%';
        });

        pageNumSpan.textContent = num;
        prevButton.disabled = num <= 1;
        nextButton.disabled = num >= pdfDoc.numPages;
    }

    // Fungsi untuk fit width
    function fitToWidth() {
        if (!pdfDoc) return;

        pdfDoc.getPage(pageNum).then(page => {
            const viewport = page.getViewport({ scale: 1 });
            const containerWidth = pdfContainer.clientWidth - 40;
            const newScale = containerWidth / viewport.width;
            renderPage(pageNum, newScale);
        });
    }

    // Fungsi zoom
    function zoomIn() {
        const newScale = Math.min(scale * 1.2, 3); // max 300%
        renderPage(pageNum, newScale);
    }

    function zoomOut() {
        const newScale = Math.max(scale / 1.2, 0.5); // min 50%
        renderPage(pageNum, newScale);
    }

    // Fungsi fullscreen
    function toggleFullscreen() {
        if (!isFullscreen) {
            mainContainer.classList.add('fullscreen-mode');
            pdfContainer.style.height = 'calc(100vh - 60px)';
            fullscreenButton.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            `;
        } else {
            mainContainer.classList.remove('fullscreen-mode');
            pdfContainer.style.height = 'calc(100vh - 200px)';
            fullscreenButton.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4h4M4 16v4h4m8-16h4v4m-4 12h4v-4"></path>
                </svg>
            `;
        }
        isFullscreen = !isFullscreen;

        // Re-render untuk menyesuaikan ukuran
        setTimeout(() => {
            renderPage(pageNum);
        }, 100);
    }

    // Touch support untuk pan/drag
    function initTouchSupport() {
        canvasWrapper.addEventListener('mousedown', startDragging);
        canvasWrapper.addEventListener('touchstart', startDragging, { passive: false });

        canvasWrapper.addEventListener('mousemove', drag);
        canvasWrapper.addEventListener('touchmove', drag, { passive: false });

        canvasWrapper.addEventListener('mouseup', stopDragging);
        canvasWrapper.addEventListener('touchend', stopDragging);
        canvasWrapper.addEventListener('mouseleave', stopDragging);
    }

    function startDragging(e) {
        isDragging = true;
        const clientX = e.clientX || e.touches[0].clientX;
        const clientY = e.clientY || e.touches[0].clientY;
        startX = clientX - canvasWrapper.offsetLeft;
        startY = clientY - canvasWrapper.offsetTop;
        scrollLeft = canvasWrapper.scrollLeft;
        scrollTop = canvasWrapper.scrollTop;
    }

    function drag(e) {
        if (!isDragging) return;
        e.preventDefault();
        const clientX = e.clientX || e.touches[0].clientX;
        const clientY = e.clientY || e.touches[0].clientY;
        const x = clientX - canvasWrapper.offsetLeft;
        const y = clientY - canvasWrapper.offsetTop;
        const walkX = (x - startX) * 2;
        const walkY = (y - startY) * 2;
        canvasWrapper.scrollLeft = scrollLeft - walkX;
        canvasWrapper.scrollTop = scrollTop - walkY;
    }

    function stopDragging() {
        isDragging = false;
    }

    // Event listeners
    prevButton.addEventListener('click', () => {
        if (pageNum <= 1) return;
        pageNum--;
        renderPage(pageNum);
    });

    nextButton.addEventListener('click', () => {
        if (pageNum >= pdfDoc.numPages) return;
        pageNum++;
        renderPage(pageNum);
    });

    zoomInButton.addEventListener('click', zoomIn);
    zoomOutButton.addEventListener('click', zoomOut);
    fitWidthButton.addEventListener('click', fitToWidth);
    fullscreenButton.addEventListener('click', toggleFullscreen);

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft' && pageNum > 1) {
            pageNum--;
            renderPage(pageNum);
        } else if (e.key === 'ArrowRight' && pageNum < pdfDoc.numPages) {
            pageNum++;
            renderPage(pageNum);
        } else if (e.key === 'Escape' && isFullscreen) {
            toggleFullscreen();
        }
    });

    // Pinch-to-zoom untuk mobile
    let initialDistance = 0;
    let initialScale = 1;

    canvasWrapper.addEventListener('touchstart', (e) => {
        if (e.touches.length === 2) {
            const touch1 = e.touches[0];
            const touch2 = e.touches[1];
            initialDistance = Math.hypot(
                touch2.clientX - touch1.clientX,
                touch2.clientY - touch1.clientY
            );
            initialScale = scale;
        }
    }, { passive: true });

    canvasWrapper.addEventListener('touchmove', (e) => {
        if (e.touches.length === 2) {
            e.preventDefault();
            const touch1 = e.touches[0];
            const touch2 = e.touches[1];
            const currentDistance = Math.hypot(
                touch2.clientX - touch1.clientX,
                touch2.clientY - touch1.clientY
            );
            const newScale = Math.max(0.5, Math.min(3, initialScale * (currentDistance / initialDistance)));
            renderPage(pageNum, newScale);
        }
    }, { passive: false });

    // Window resize handler
    window.addEventListener('resize', () => {
        if (pdfDoc) {
            setTimeout(() => {
                renderPage(pageNum);
            }, 100);
        }
    });

    // Load PDF
    pdfjsLib.getDocument(url).promise.then(doc => {
        pdfDoc = doc;

        loadingSpinner.classList.add('hidden');
        canvasWrapper.classList.remove('hidden');
        pageCountSpan.textContent = pdfDoc.numPages;

        renderPage(pageNum);
        initTouchSupport();
    }).catch(err => {
        console.error('Error loading PDF:', err);
        loadingSpinner.innerHTML = '<div class="text-center"><p class="text-red-500 font-semibold mb-2">Gagal memuat PDF</p><p class="text-gray-500 text-sm">Silakan coba lagi atau hubungi administrator</p></div>';
    });

</script>
@endpush
