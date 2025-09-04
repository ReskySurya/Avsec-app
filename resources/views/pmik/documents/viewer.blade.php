@extends('layouts.app')

@section('title', 'Lihat Dokumen: ' . $document->title)

@push('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Mobile-first PDF Container */
    .pdf-main-container {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #000;
        color: white;
        overflow: hidden;
        touch-action: manipulation;
    }

    /* Desktop sidebar adjustment */
    @media (min-width: 1024px) {
        .pdf-main-container {
            left: 256px;
            /* 16rem = 256px (sidebar width) */
        }
    }

    /* Header yang mobile-friendly */
    .pdf-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        background: rgba(0, 0, 0, 0.9);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: env(safe-area-inset-top, 0) 16px 12px 16px;
        transition: transform 0.3s ease;
    }

    .pdf-header.hidden {
        transform: translateY(-100%);
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 44px;
    }

    .back-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        border-radius: 8px;
        color: white;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        min-height: 44px;
    }

    .back-btn:active {
        transform: scale(0.95);
        background: rgba(255, 255, 255, 0.2);
    }

    .document-title {
        flex: 1;
        margin: 0 16px;
        font-size: 16px;
        font-weight: 600;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: white;
    }

    .header-actions {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        width: 44px;
        height: 44px;
        border: none;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .action-btn:active {
        transform: scale(0.95);
        background: rgba(255, 255, 255, 0.2);
    }

    /* Main PDF Container */
    .pdf-container {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #000;
        overflow: hidden;
        touch-action: pan-x pan-y pinch-zoom;
        padding-top: calc(env(safe-area-inset-top, 0) + 68px);
        padding-bottom: calc(env(safe-area-inset-bottom, 0) + 80px);
    }

    .pdf-viewport {
        width: 100%;
        height: 100%;
        overflow: auto;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }

    .page-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 16px;
        gap: 16px;
        min-height: 100%;
    }

    .pdf-page {
        background: white;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        border-radius: 8px;
        overflow: hidden;
        max-width: 100%;
        transition: transform 0.2s ease;
    }

    .pdf-page canvas {
        display: block;
        width: 100%;
        height: auto;
        max-width: 100%;
    }

    /* Bottom Controls */
    .bottom-controls {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        background: rgba(0, 0, 0, 0.9);
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding: 12px 16px calc(env(safe-area-inset-bottom, 0) + 12px) 16px;
        transition: transform 0.3s ease;
    }

    .bottom-controls.hidden {
        transform: translateY(100%);
    }

    .controls-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .page-navigation {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .nav-btn {
        width: 48px;
        height: 48px;
        border: none;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
    }

    .nav-btn:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .nav-btn:not(:disabled):active {
        transform: scale(0.9);
        background: rgba(255, 255, 255, 0.25);
    }

    .page-info {
        background: rgba(255, 255, 255, 0.1);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        min-width: 80px;
        text-align: center;
        white-space: nowrap;
    }

    .zoom-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .zoom-btn {
        width: 40px;
        height: 40px;
        border: none;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
    }

    .zoom-btn:active {
        transform: scale(0.9);
        background: rgba(255, 255, 255, 0.25);
    }

    .zoom-level {
        font-size: 12px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.8);
        min-width: 40px;
        text-align: center;
    }

    /* Loading State */
    .loading-container {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-top: 3px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 16px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Page Number Overlay */
    .page-overlay {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 12px 20px;
        border-radius: 20px;
        font-size: 18px;
        font-weight: 600;
        z-index: 1001;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .page-overlay.show {
        opacity: 1;
    }

    /* Gesture hints */
    .gesture-hint {
        position: fixed;
        bottom: 120px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 8px 16px;
        border-radius: 16px;
        font-size: 12px;
        z-index: 999;
        animation: fadeInOut 3s ease-in-out;
    }

    @keyframes fadeInOut {

        0%,
        100% {
            opacity: 0;
        }

        20%,
        80% {
            opacity: 1;
        }
    }

    /* Touch feedback */
    .touch-feedback {
        position: fixed;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        pointer-events: none;
        transform: scale(0);
        animation: touchRipple 0.6s ease-out;
        z-index: 1002;
    }

    @keyframes touchRipple {
        0% {
            transform: scale(0);
            opacity: 1;
        }

        100% {
            transform: scale(1);
            opacity: 0;
        }
    }

    /* Portrait/Landscape specific styles */
    @media (orientation: landscape) {
        .pdf-container {
            padding-top: calc(env(safe-area-inset-top, 0) + 60px);
            padding-bottom: calc(env(safe-area-inset-bottom, 0) + 70px);
        }

        .page-container {
            padding: 8px;
        }
    }

    /* Improved scrollbar */
    .pdf-viewport::-webkit-scrollbar {
        width: 4px;
    }

    .pdf-viewport::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }

    .pdf-viewport::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
    }
</style>
@endpush

@section('content')
<div class="pdf-main-container">
    <!-- Header -->
    <header class="pdf-header" id="pdf-header">
        <div class="header-content mt-20">
            <a href="{{ route('folders.show', $document->folder) }}" class="back-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15,18 9,12 15,6"></polyline>
                </svg>
                Kembali
            </a>
            <h1 class="document-title">{{ $document->title }}</h1>
            <div class="header-actions">
                <button class="action-btn" id="download-btn" title="Download">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7,10 12,15 17,10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Main PDF Container -->
    <main class="pdf-container">
        <div class="pdf-viewport" id="pdf-viewport">
            <div class="page-container" id="page-container">
                <!-- Loading State -->
                <div class="loading-container" id="loading-container">
                    <div class="loading-spinner"></div>
                    <p>Memuat dokumen...</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Bottom Controls -->
    <div class="bottom-controls" id="bottom-controls">
        <div class="controls-row">
            <!-- Navigation -->
            <div class="page-navigation">
                <button class="nav-btn" id="prev-btn" disabled>‹</button>
                <div class="page-info">
                    <span id="current-page">1</span> / <span id="total-pages">1</span>
                </div>
                <button class="nav-btn" id="next-btn">›</button>
            </div>

            <!-- Zoom Controls -->
            <div class="zoom-controls">
                <button class="zoom-btn" id="zoom-out-btn">−</button>
                <div class="zoom-level" id="zoom-level">100%</div>
                <button class="zoom-btn" id="zoom-in-btn">+</button>
            </div>
        </div>
    </div>

    <!-- Page Overlay for quick feedback -->
    <div class="page-overlay" id="page-overlay">
        <span id="page-overlay-text">Halaman 1</span>
    </div>

    <!-- Gesture Hint -->
    <div class="gesture-hint" id="gesture-hint" style="display: none;">
        Ketuk untuk menyembunyikan kontrol
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('pdfjs/build/pdf.mjs') }}" type="module"></script>
<script type="module">
    const { pdfjsLib } = globalThis;
pdfjsLib.GlobalWorkerOptions.workerSrc = `{{ asset('pdfjs/build/pdf.worker.mjs') }}`;

class MobilePDFViewer {
    constructor() {
        this.pdfDoc = null;
        this.currentPage = 1;
        this.totalPages = 0;
        this.scale = 1;
        this.minScale = 0.5;
        this.maxScale = 3;
        this.isLoading = false;
        this.controlsVisible = true;
        this.lastTouchTime = 0;
        this.pages = new Map();
        this.url = '{{ route('documents.view', $document) }}';

        this.initElements();
        this.initEventListeners();
        this.loadPDF();
    }

    initElements() {
        this.elements = {
            header: document.getElementById('pdf-header'),
            bottomControls: document.getElementById('bottom-controls'),
            viewport: document.getElementById('pdf-viewport'),
            pageContainer: document.getElementById('page-container'),
            loadingContainer: document.getElementById('loading-container'),
            currentPageSpan: document.getElementById('current-page'),
            totalPagesSpan: document.getElementById('total-pages'),
            prevBtn: document.getElementById('prev-btn'),
            nextBtn: document.getElementById('next-btn'),
            zoomInBtn: document.getElementById('zoom-in-btn'),
            zoomOutBtn: document.getElementById('zoom-out-btn'),
            zoomLevel: document.getElementById('zoom-level'),
            pageOverlay: document.getElementById('page-overlay'),
            pageOverlayText: document.getElementById('page-overlay-text'),
            gestureHint: document.getElementById('gesture-hint'),
            downloadBtn: document.getElementById('download-btn')
        };
    }

    initEventListeners() {
        // Navigation
        this.elements.prevBtn.addEventListener('click', () => this.goToPage(this.currentPage - 1));
        this.elements.nextBtn.addEventListener('click', () => this.goToPage(this.currentPage + 1));

        // Zoom
        this.elements.zoomInBtn.addEventListener('click', () => this.zoomIn());
        this.elements.zoomOutBtn.addEventListener('click', () => this.zoomOut());

        // Download
        this.elements.downloadBtn.addEventListener('click', () => {
            window.open(this.url + '?download=1', '_blank');
        });

        // Touch controls toggle
        this.elements.viewport.addEventListener('click', (e) => this.handleViewportClick(e));

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleKeydown(e));

        // Touch gestures
        this.initTouchGestures();

        // Scroll handling
        this.elements.viewport.addEventListener('scroll', () => this.handleScroll());

        // Resize
        window.addEventListener('resize', () => this.handleResize());
        window.addEventListener('orientationchange', () => {
            setTimeout(() => this.handleResize(), 500);
        });
    }

    initTouchGestures() {
        let touchStartTime = 0;
        let touchCount = 0;
        let initialDistance = 0;
        let initialScale = 1;

        this.elements.viewport.addEventListener('touchstart', (e) => {
            touchStartTime = Date.now();

            if (e.touches.length === 2) {
                const touch1 = e.touches[0];
                const touch2 = e.touches[1];
                initialDistance = Math.hypot(
                    touch2.clientX - touch1.clientX,
                    touch2.clientY - touch1.clientY
                );
                initialScale = this.scale;
            }
        }, { passive: true });

        this.elements.viewport.addEventListener('touchmove', (e) => {
            if (e.touches.length === 2) {
                e.preventDefault();
                const touch1 = e.touches[0];
                const touch2 = e.touches[1];
                const currentDistance = Math.hypot(
                    touch2.clientX - touch1.clientX,
                    touch2.clientY - touch1.clientY
                );
                const newScale = Math.max(this.minScale,
                    Math.min(this.maxScale, initialScale * (currentDistance / initialDistance))
                );
                this.setZoom(newScale);
            }
        }, { passive: false });

        this.elements.viewport.addEventListener('touchend', (e) => {
            const touchDuration = Date.now() - touchStartTime;

            // Double tap to zoom
            if (touchDuration < 300 && e.changedTouches.length === 1) {
                touchCount++;
                setTimeout(() => {
                    if (touchCount === 2) {
                        this.handleDoubleTap();
                    }
                    touchCount = 0;
                }, 300);
            }
        }, { passive: true });
    }

    handleViewportClick(e) {
        const now = Date.now();
        if (now - this.lastTouchTime < 300) return;
        this.lastTouchTime = now;

        this.createTouchFeedback(e.clientX, e.clientY);
        this.toggleControls();
    }

    createTouchFeedback(x, y) {
        const feedback = document.createElement('div');
        feedback.className = 'touch-feedback';
        feedback.style.left = (x - 30) + 'px';
        feedback.style.top = (y - 30) + 'px';
        document.body.appendChild(feedback);

        setTimeout(() => {
            if (document.body.contains(feedback)) {
                document.body.removeChild(feedback);
            }
        }, 600);
    }

    toggleControls() {
        this.controlsVisible = !this.controlsVisible;

        if (this.controlsVisible) {
            this.elements.header.classList.remove('hidden');
            this.elements.bottomControls.classList.remove('hidden');
        } else {
            this.elements.header.classList.add('hidden');
            this.elements.bottomControls.classList.add('hidden');
        }
    }

    handleDoubleTap() {
        if (this.scale === 1) {
            this.setZoom(2);
        } else {
            this.fitToScreen();
        }
    }

    handleKeydown(e) {
        switch(e.key) {
            case 'ArrowLeft':
                this.goToPage(this.currentPage - 1);
                break;
            case 'ArrowRight':
                this.goToPage(this.currentPage + 1);
                break;
            case '+':
                this.zoomIn();
                break;
            case '-':
                this.zoomOut();
                break;
            case 'f':
                this.fitToScreen();
                break;
            case 'Escape':
                if (!this.controlsVisible) {
                    this.toggleControls();
                }
                break;
        }
    }

    async loadPDF() {
        this.isLoading = true;
        try {
            this.pdfDoc = await pdfjsLib.getDocument(this.url).promise;
            this.totalPages = this.pdfDoc.numPages;
            this.elements.totalPagesSpan.textContent = this.totalPages;

            // Load first few pages
            await this.renderPages(1, Math.min(3, this.totalPages));
            this.hideLoading();
            this.showGestureHint();

        } catch (error) {
            this.showError('Gagal memuat PDF: ' + error.message);
        } finally {
            this.isLoading = false;
        }
    }

    async renderPages(startPage, endPage) {
        const promises = [];
        for (let pageNum = startPage; pageNum <= endPage; pageNum++) {
            promises.push(this.renderPage(pageNum));
        }
        await Promise.all(promises);
    }

    async renderPage(pageNum) {
        if (!this.pdfDoc || this.pages.has(pageNum)) return;

        const page = await this.pdfDoc.getPage(pageNum);
        const viewport = page.getViewport({ scale: this.scale });

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        // High DPI support
        const pixelRatio = window.devicePixelRatio || 1;
        canvas.width = viewport.width * pixelRatio;
        canvas.height = viewport.height * pixelRatio;
        canvas.style.width = viewport.width + 'px';
        canvas.style.height = viewport.height + 'px';
        ctx.scale(pixelRatio, pixelRatio);

        const pageDiv = document.createElement('div');
        pageDiv.className = 'pdf-page';
        pageDiv.id = `page-${pageNum}`;
        pageDiv.appendChild(canvas);

        // Insert at correct position
        const existingPages = Array.from(this.elements.pageContainer.children)
            .filter(el => el.classList.contains('pdf-page'));

        let inserted = false;
        for (const existingPage of existingPages) {
            const existingPageNum = parseInt(existingPage.id.split('-')[1]);
            if (pageNum < existingPageNum) {
                this.elements.pageContainer.insertBefore(pageDiv, existingPage);
                inserted = true;
                break;
            }
        }

        if (!inserted) {
            this.elements.pageContainer.appendChild(pageDiv);
        }

        // Render
        await page.render({
            canvasContext: ctx,
            viewport: viewport
        }).promise;

        this.pages.set(pageNum, { element: pageDiv, canvas: canvas, page: page });
    }

    goToPage(pageNum) {
        if (pageNum < 1 || pageNum > this.totalPages) return;

        this.currentPage = pageNum;
        this.updatePageInfo();

        const pageElement = document.getElementById(`page-${pageNum}`);
        if (pageElement) {
            pageElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        this.preloadPages();
        this.showPageOverlay(pageNum);
    }

    preloadPages() {
        const toLoad = [];
        for (let i = -2; i <= 2; i++) {
            const pageNum = this.currentPage + i;
            if (pageNum >= 1 && pageNum <= this.totalPages && !this.pages.has(pageNum)) {
                toLoad.push(pageNum);
            }
        }

        toLoad.forEach(pageNum => {
            setTimeout(() => this.renderPage(pageNum), 100);
        });
    }

    updatePageInfo() {
        this.elements.currentPageSpan.textContent = this.currentPage;
        this.elements.prevBtn.disabled = this.currentPage <= 1;
        this.elements.nextBtn.disabled = this.currentPage >= this.totalPages;
    }

    zoomIn() {
        const newScale = Math.min(this.maxScale, this.scale * 1.25);
        this.setZoom(newScale);
    }

    zoomOut() {
        const newScale = Math.max(this.minScale, this.scale / 1.25);
        this.setZoom(newScale);
    }

    setZoom(scale) {
        this.scale = scale;
        this.elements.zoomLevel.textContent = Math.round(scale * 100) + '%';
        this.rerenderVisiblePages();
    }

    fitToScreen() {
        if (!this.pdfDoc) return;

        this.pdfDoc.getPage(1).then(page => {
            const viewport = page.getViewport({ scale: 1 });
            const containerWidth = this.elements.viewport.clientWidth - 32;
            const newScale = containerWidth / viewport.width;
            this.setZoom(newScale);
        });
    }

    rerenderVisiblePages() {
        this.pages.clear();
        this.elements.pageContainer.innerHTML = '';

        const startPage = Math.max(1, this.currentPage - 1);
        const endPage = Math.min(this.totalPages, this.currentPage + 1);
        this.renderPages(startPage, endPage);
    }

    showPageOverlay(pageNum) {
        this.elements.pageOverlayText.textContent = `Halaman ${pageNum}`;
        this.elements.pageOverlay.classList.add('show');

        setTimeout(() => {
            this.elements.pageOverlay.classList.remove('show');
        }, 1000);
    }

    showGestureHint() {
        this.elements.gestureHint.style.display = 'block';
        setTimeout(() => {
            this.elements.gestureHint.style.display = 'none';
        }, 3000);
    }

    hideLoading() {
        this.elements.loadingContainer.style.display = 'none';
    }

    showError(message) {
        this.elements.loadingContainer.innerHTML = `
            <div style="color: #ff6b6b; text-align: center;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom: 16px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
                <p style="font-weight: 600; margin-bottom: 8px;">Oops! Terjadi Kesalahan</p>
                <p style="font-size: 14px; opacity: 0.8;">${message}</p>
            </div>
        `;
    }

    handleScroll() {
        // Auto-detect current page based on scroll position
        const scrollTop = this.elements.viewport.scrollTop;
        const viewportHeight = this.elements.viewport.clientHeight;
        const centerY = scrollTop + viewportHeight / 2;

        let currentPage = 1;
        this.pages.forEach((pageData, pageNum) => {
            const pageRect = pageData.element.getBoundingClientRect();
            const pageTop = pageRect.top + scrollTop;
            const pageBottom = pageTop + pageRect.height;

            if (centerY >= pageTop && centerY <= pageBottom) {
                currentPage = pageNum;
            }
        });

        if (currentPage !== this.currentPage) {
            this.currentPage = currentPage;
            this.updatePageInfo();
        }
    }

    handleResize() {
        if (this.scale !== 1) {
            this.rerenderVisiblePages();
        }
    }
}

// Initialize the viewer
document.addEventListener('DOMContentLoaded', () => {
    new MobilePDFViewer();
});

</script>
@endpush
