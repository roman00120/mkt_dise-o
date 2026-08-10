<!-- Global Lightbox / Modal File Viewer -->
<div id="tg-lightbox-modal" class="fixed inset-0 z-[100] hidden flex-col justify-between bg-slate-950/95 backdrop-blur-xl p-4 sm:p-6 transition-all duration-300" role="dialog" aria-modal="true" aria-label="Visor de archivos">

    <!-- Top Bar Header -->
    <div class="flex items-center justify-between gap-4 border-b border-slate-800/80 pb-4">
        <div class="flex items-center gap-3 min-w-0">
            <span id="tg-lightbox-icon" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-600/20 text-red-400 font-bold text-lg border border-red-500/30">
                📁
            </span>
            <div class="min-w-0">
                <h3 id="tg-lightbox-title" class="truncate text-base font-extrabold text-white leading-tight">Nombre del archivo</h3>
                <p id="tg-lightbox-meta" class="text-xs font-medium text-slate-400 mt-0.5">Categoría · Tamaño</p>
            </div>
        </div>

        <!-- Right Header Action Controls -->
        <div class="flex items-center gap-2 shrink-0">
            <!-- Zoom In -->
            <button id="tg-lightbox-zoom-in" type="button" class="hidden sm:inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:bg-slate-800 transition" title="Acercar (+)">
                🔍+
            </button>
            <!-- Zoom Out -->
            <button id="tg-lightbox-zoom-out" type="button" class="hidden sm:inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:bg-slate-800 transition" title="Alejar (-)">
                🔍-
            </button>
            <!-- Rotate -->
            <button id="tg-lightbox-rotate" type="button" class="hidden sm:inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:bg-slate-800 transition" title="Rotar 90°">
                🔄
            </button>
            <!-- Download Button -->
            <a id="tg-lightbox-download" href="#" target="_blank" download class="inline-flex h-9 items-center gap-2 rounded-lg bg-red-600 hover:bg-red-500 px-3.5 text-xs font-bold text-white transition shadow-md">
                <span>⬇️</span>
                <span class="hidden sm:inline">Descargar</span>
            </a>
            <!-- Close Button -->
            <button id="tg-lightbox-close" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-800 text-slate-300 hover:bg-red-600 hover:text-white transition font-bold text-lg" title="Cerrar (Esc)">
                ✕
            </button>
        </div>
    </div>

    <!-- Main Viewport Body -->
    <div id="tg-lightbox-body" class="relative flex-1 flex items-center justify-center overflow-hidden my-4">
        <!-- Spinner -->
        <div id="tg-lightbox-loader" class="absolute inset-0 flex items-center justify-center bg-slate-950/60 z-10">
            <div class="h-10 w-10 animate-spin rounded-full border-4 border-slate-700 border-t-red-600"></div>
        </div>

        <!-- Image Content Container -->
        <img id="tg-lightbox-img" src="" alt="Previsualización" class="hidden max-h-full max-w-full object-contain rounded-lg shadow-2xl transition-transform duration-200 select-none">

        <!-- Video Player Container -->
        <video id="tg-lightbox-video" controls class="hidden max-h-full max-w-full rounded-lg shadow-2xl">
            <source src="" type="video/mp4">
            Tu navegador no soporta la reproducción de video.
        </video>

        <!-- PDF / Document Iframe Container -->
        <iframe id="tg-lightbox-iframe" src="" class="hidden w-full h-full rounded-lg border border-slate-800 bg-white"></iframe>

        <!-- Fallback Card for non-previewable files -->
        <div id="tg-lightbox-fallback" class="hidden flex-col items-center justify-center p-8 text-center bg-slate-900/90 border border-slate-800 rounded-2xl max-w-md shadow-2xl">
            <div class="h-16 w-16 rounded-2xl bg-red-600/10 border border-red-500/20 text-red-500 flex items-center justify-center text-3xl mb-4 font-bold">
                📦
            </div>
            <h4 class="text-lg font-extrabold text-white mb-1">Previsualización no disponible</h4>
            <p class="text-xs text-slate-400 mb-6">Este tipo de archivo requiere software especializado (ej. Photoshop, Illustrator, CAD, ZIP). Puedes descargarlo directamente a tu equipo.</p>
            <a id="tg-lightbox-fallback-download" href="#" target="_blank" download class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-500 px-6 py-3 text-sm font-bold text-white transition shadow-lg">
                ⬇️ Descargar archivo original
            </a>
        </div>
    </div>

    <!-- Bottom Caption Bar -->
    <div class="flex items-center justify-between border-t border-slate-800/80 pt-3 text-xs text-slate-400">
        <span>TG Creative Hub · Visor Integrado</span>
        <span class="hidden sm:inline">Presiona <kbd class="px-1.5 py-0.5 rounded bg-slate-800 border border-slate-700 text-slate-200 font-mono text-[10px]">Esc</kbd> para cerrar</span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('tg-lightbox-modal');
    const closeBtn = document.getElementById('tg-lightbox-close');
    const loader = document.getElementById('tg-lightbox-loader');
    const img = document.getElementById('tg-lightbox-img');
    const video = document.getElementById('tg-lightbox-video');
    const iframe = document.getElementById('tg-lightbox-iframe');
    const fallback = document.getElementById('tg-lightbox-fallback');
    const fallbackDl = document.getElementById('tg-lightbox-fallback-download');
    const titleEl = document.getElementById('tg-lightbox-title');
    const metaEl = document.getElementById('tg-lightbox-meta');
    const iconEl = document.getElementById('tg-lightbox-icon');
    const downloadEl = document.getElementById('tg-lightbox-download');
    const zoomInBtn = document.getElementById('tg-lightbox-zoom-in');
    const zoomOutBtn = document.getElementById('tg-lightbox-zoom-out');
    const rotateBtn = document.getElementById('tg-lightbox-rotate');

    let currentZoom = 1;
    let currentRotation = 0;

    function resetTransforms() {
        currentZoom = 1;
        currentRotation = 0;
        applyTransform();
    }

    function applyTransform() {
        img.style.transform = `scale(${currentZoom}) rotate(${currentRotation}deg)`;
    }

    if (zoomInBtn) {
        zoomInBtn.addEventListener('click', () => {
            currentZoom = Math.min(currentZoom + 0.25, 3);
            applyTransform();
        });
    }

    if (zoomOutBtn) {
        zoomOutBtn.addEventListener('click', () => {
            currentZoom = Math.max(currentZoom - 0.25, 0.5);
            applyTransform();
        });
    }

    if (rotateBtn) {
        rotateBtn.addEventListener('click', () => {
            currentRotation = (currentRotation + 90) % 360;
            applyTransform();
        });
    }

    function hideAllViewers() {
        img.classList.add('hidden');
        video.classList.add('hidden');
        iframe.classList.add('hidden');
        fallback.classList.add('hidden');
        video.pause();
        video.src = '';
        iframe.src = '';
        img.src = '';
        resetTransforms();
    }

    function openLightbox(fileUrl, downloadUrl, fileName, mimeType, metaText, icon) {
        hideAllViewers();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        titleEl.textContent = fileName || 'Archivo';
        metaEl.textContent = metaText || 'Detalle de archivo';
        iconEl.textContent = icon || '📁';
        downloadEl.href = downloadUrl || fileUrl;
        fallbackDl.href = downloadUrl || fileUrl;

        loader.classList.remove('hidden');

        const cleanMime = (mimeType || '').toLowerCase();
        const ext = (fileName.split('.').pop() || '').toLowerCase();

        const isImage = cleanMime.startsWith('image/') || ['jpg','jpeg','png','gif','webp','svg'].includes(ext);
        const isVideo = cleanMime.startsWith('video/') || ['mp4','webm','mov'].includes(ext);
        const isPdf = cleanMime.includes('pdf') || ext === 'pdf';

        if (isImage) {
            iconEl.textContent = '🖼️';
            img.onload = () => loader.classList.add('hidden');
            img.onerror = () => {
                loader.classList.add('hidden');
                hideAllViewers();
                fallback.classList.remove('hidden');
            };
            img.src = fileUrl;
            img.classList.remove('hidden');
        } else if (isVideo) {
            iconEl.textContent = '🎬';
            video.src = fileUrl;
            video.onloadeddata = () => loader.classList.add('hidden');
            video.classList.remove('hidden');
        } else if (isPdf) {
            iconEl.textContent = '📄';
            iframe.onload = () => loader.classList.add('hidden');
            iframe.src = fileUrl;
            iframe.classList.remove('hidden');
        } else {
            iconEl.textContent = '📦';
            loader.classList.add('hidden');
            fallback.classList.remove('hidden');
        }
    }

    function closeLightbox() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        hideAllViewers();
    }

    if (closeBtn) closeBtn.addEventListener('click', closeLightbox);

    modal.addEventListener('click', (e) => {
        if (e.target === modal || e.target === document.getElementById('tg-lightbox-body')) {
            closeLightbox();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeLightbox();
        }
    });

    // Global listener for elements with data-lightbox-trigger
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-lightbox-trigger]');
        if (trigger) {
            e.preventDefault();
            const previewUrl = trigger.getAttribute('data-preview-url');
            const downloadUrl = trigger.getAttribute('data-download-url') || previewUrl;
            const fileName = trigger.getAttribute('data-file-name') || 'Archivo';
            const mimeType = trigger.getAttribute('data-file-mime') || '';
            const metaText = trigger.getAttribute('data-file-meta') || '';
            const icon = trigger.getAttribute('data-file-icon') || '📁';
            openLightbox(previewUrl, downloadUrl, fileName, mimeType, metaText, icon);
        }
    });
});
</script>
