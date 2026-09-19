import './bootstrap';

import Alpine from 'alpinejs';
import {
    Activity, AlarmClock, ArrowDown, ArrowLeft, ArrowRight, ArrowUp, Ban, Bell, Box,
    BriefcaseBusiness, Check, CheckCheck, CheckCircle, ChevronDown, Circle, CircleCheck,
    CircleX, Clock3, Download, Eye, EyeOff, FileEdit, FileText, Gauge, Inbox, Info,
    Layers3, LayoutDashboard, LoaderCircle, LockKeyhole, Mail, Maximize2, Menu,
    LogOut, MessageCircle, Minus, MoreHorizontal, Palette, Paperclip, PauseCircle, PenTool, Plus,
    PlayCircle, RefreshCw, ScanSearch, Search, SearchCheck, ShieldCheck, TextCursorInput,
    TriangleAlert, UploadCloud, UserCheck, Video, X, createIcons,
} from 'lucide';

window.Alpine = Alpine;
document.querySelectorAll('[x-cloak]').forEach((element) => {
    element.style.display = 'none';
});
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('p').forEach((element) => {
        if (element.textContent.includes('hasta 25 MB')) element.textContent = element.textContent.replace('hasta 25 MB', 'hasta 100 MB');
    });
    createIcons({ icons: {
        Activity, AlarmClock, ArrowDown, ArrowLeft, ArrowRight, ArrowUp, Ban, Bell, Box,
        BriefcaseBusiness, Check, CheckCheck, CheckCircle, ChevronDown, Circle, CircleCheck,
        CircleX, Clock3, Download, Eye, EyeOff, FileEdit, FileText, Gauge, Inbox, Info,
        Layers3, LayoutDashboard, LoaderCircle, LockKeyhole, Mail, Maximize2, Menu,
        LogOut, MessageCircle, Minus, MoreHorizontal, Palette, Paperclip, PauseCircle, PenTool, Plus,
        PlayCircle, RefreshCw, ScanSearch, Search, SearchCheck, ShieldCheck, TextCursorInput,
        TriangleAlert, UploadCloud, UserCheck, Video, X,
    } });

    document.querySelectorAll('form[action*="/submit"]').forEach((form) => {
        const submitButton = form.querySelector('button[type="submit"]');
        if (!submitButton || form.dataset.aiBound) return;
        form.dataset.aiBound = '1';
        submitButton.childNodes.forEach((node) => { if (node.nodeType === Node.TEXT_NODE && node.textContent.trim()) node.textContent = 'Revisar y enviar a Diseño '; });
        let allowSubmit = false;
        const getText = () => form.querySelector('textarea[name="description"]')?.value || '';
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 p-4';
        modal.innerHTML = '<div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl border border-slate-700 bg-slate-900 p-6 shadow-2xl"><div class="flex items-start justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-wider text-red-400">Revisión inteligente</p><h2 class="mt-1 text-xl font-bold text-white">Revisión de la solicitud</h2></div><button type="button" data-ai-close class="text-2xl text-slate-400">×</button></div><p data-ai-status class="mt-3 text-sm text-slate-300">Revisando solicitud con IA…</p><div data-ai-result class="mt-5 hidden space-y-4"></div><div class="mt-6 flex flex-wrap justify-end gap-2"><button type="button" data-ai-retry class="hidden rounded border border-slate-600 px-4 py-2 text-sm">Volver a revisar</button><button type="button" data-ai-edit class="rounded border border-slate-600 px-4 py-2 text-sm">Editar solicitud</button><button type="button" data-ai-send-manual class="hidden rounded bg-amber-600 px-4 py-2 text-sm font-semibold">Enviar de todos modos</button><button type="button" data-ai-send class="hidden rounded bg-red-600 px-4 py-2 text-sm font-semibold">Enviar a Diseño</button></div></div>';
        document.body.append(modal);
        const resultBox = modal.querySelector('[data-ai-result]');
        const statusBox = modal.querySelector('[data-ai-status]');
        const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char]);
        const list = (label, values) => values?.length ? `<div><h3 class="font-semibold text-white">${escapeHtml(label)}</h3><ul class="mt-1 list-disc space-y-1 pl-5 text-sm text-slate-300">${values.map((v) => `<li>${escapeHtml(v)}</li>`).join('')}</ul></div>` : '';
        const runReview = async () => {
            modal.classList.remove('hidden'); modal.classList.add('flex'); resultBox.classList.add('hidden'); statusBox.textContent = 'Revisando solicitud con IA…';
            try {
                const response = await fetch(form.action.replace(/\/submit$/, '/ai-review'), { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' }, body: new FormData(form) });
                const data = await response.json();
                if (!response.ok || !data.ok) throw new Error(data.message || 'No fue posible revisar la solicitud.');
                const r = data.result; statusBox.textContent = `Resultado: ${r.status === 'ready' ? 'Lista para enviar' : r.status === 'warning' ? 'Puede enviarse con observaciones' : 'Falta información importante'}`;
                resultBox.innerHTML = `<div class="rounded-lg bg-slate-950 p-4"><h3 class="font-semibold text-white">Resumen</h3><p class="mt-1 text-sm text-slate-300">${escapeHtml(r.summary)}</p></div><div><h3 class="font-semibold text-white">Texto original</h3><p class="mt-1 whitespace-pre-line rounded-lg bg-slate-950 p-4 text-sm text-slate-400">${escapeHtml(getText() || 'Sin descripción')}</p></div><div><h3 class="font-semibold text-white">Versión corregida sugerida</h3><p class="mt-1 whitespace-pre-line rounded-lg bg-slate-950 p-4 text-sm text-slate-300">${escapeHtml(r.corrected_text)}</p><button type="button" data-ai-use class="mt-2 rounded border border-emerald-500/50 px-3 py-2 text-xs text-emerald-300">Usar versión corregida</button></div>${list('Errores ortográficos', r.spelling_corrections?.map((x) => `${x.original} → ${x.corrected}`))}${list('Información faltante', r.missing_information)}${list('Ambigüedades', r.ambiguous_instructions)}${list('Contradicciones', r.contradictions)}${list('Recomendaciones', r.recommendations)}`;
                resultBox.classList.remove('hidden'); modal.querySelector('[data-ai-send]').classList.toggle('hidden', r.status === 'incomplete'); modal.querySelector('[data-ai-send-manual]').classList.toggle('hidden', r.status !== 'incomplete'); modal.querySelector('[data-ai-retry]').classList.remove('hidden');
                modal.querySelector('[data-ai-use]').onclick = async () => { if (!confirm('¿Aplicar las correcciones detectadas en los campos de la solicitud?')) return; const response = await fetch(form.action.replace(/\/submit$/, '/ai-review/apply-correction'), { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', Accept: 'application/json' }, body: JSON.stringify({ corrected_text: r.corrected_text, corrections: r.spelling_corrections || [] }) }); if (response.ok) { statusBox.textContent = 'Correcciones guardadas. Actualizando…'; window.location.reload(); } else { statusBox.textContent = 'No se pudieron guardar las correcciones. Intenta nuevamente.'; } };
            } catch (error) { statusBox.textContent = error.message; modal.querySelector('[data-ai-retry]').classList.remove('hidden'); modal.querySelector('[data-ai-send-manual]').classList.remove('hidden'); }
        };
        form.addEventListener('submit', (event) => { if (allowSubmit) return; event.preventDefault(); runReview(); });
        modal.querySelector('[data-ai-close]').onclick = () => modal.classList.add('hidden');
        modal.querySelector('[data-ai-edit]').onclick = () => modal.classList.add('hidden');
        modal.querySelector('[data-ai-retry]').onclick = runReview;
        const manualSubmit = () => { allowSubmit = true; let input = form.querySelector('input[name="send_without_ai"]'); if (!input) { input = document.createElement('input'); input.type = 'hidden'; input.name = 'send_without_ai'; input.value = '1'; form.append(input); } form.submit(); };
        modal.querySelector('[data-ai-send]').onclick = manualSubmit; modal.querySelector('[data-ai-send-manual]').onclick = manualSubmit;
    });

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    document.querySelectorAll('form[action*="/app/requests/drafts/"]').forEach((form) => {
        if (form.action.includes('/files') || form.action.includes('/submit')) return;
        let timer;
        form.addEventListener('input', () => {
            clearTimeout(timer);
            const status = document.querySelector('.js-autosave-status');
            if (status) status.textContent = 'Guardando…';
            timer = setTimeout(async () => {
                const url = form.action.replace(/\/drafts\/([^/]+)$/, '/drafts/$1/autosave');
                const body = new FormData(form);
                if (!body.has('step')) body.append('step', '1');
                try {
                    await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' }, body });
                    if (status) status.textContent = 'Cambios guardados';
                } catch {
                    if (status) status.textContent = 'No pudimos guardar tus cambios.';
                }
            }, 1000);
        });
    });

    document.addEventListener('click', (event) => {
        const button = event.target.closest('button[type="button"]');
        const dialog = button?.closest('[role="dialog"]');
        if (!button || !dialog || button.textContent.trim() !== 'Volver') return;

        event.preventDefault();
        event.stopPropagation();
        const scope = dialog._x_dataStack?.[0];
        if (scope) scope.open = false;
    }, true);

    document.querySelectorAll('form.js-upload-form, form[enctype="multipart/form-data"]').forEach((form) => {
        const input = form.querySelector('input[type="file"]');
        let progress = form.querySelector('[data-upload-progress]');
        let bar = form.querySelector('[data-upload-progress-bar]');
        let status = form.querySelector('[data-upload-status]');
        if (input && !progress) {
            progress = document.createElement('div');
            progress.className = 'hidden mt-3';
            progress.dataset.uploadProgress = '';
            progress.setAttribute('aria-live', 'polite');
            progress.innerHTML = '<div class="h-2 overflow-hidden rounded-full bg-slate-800"><div data-upload-progress-bar class="h-full w-0 rounded-full bg-red-500 transition-all duration-200"></div></div><p data-upload-status class="mt-1 text-xs text-slate-400">Listo para subir.</p>';
            form.append(progress);
            bar = progress.querySelector('[data-upload-progress-bar]');
            status = progress.querySelector('[data-upload-status]');
        }
        const maxBytes = Number(form.dataset.maxBytes || 104857600);

        input?.addEventListener('change', () => {
            const file = input.files?.[0];
            if (!file) return;
            const size = file.size / 1024 / 1024;
            const label = form.querySelector('[data-upload-size]');
            if (label) label.textContent = `${size.toFixed(1)} MB de máximo ${(maxBytes / 1024 / 1024).toFixed(0)} MB`;
            if (file.size > maxBytes) {
                input.setCustomValidity(`El archivo supera el máximo de ${(maxBytes / 1024 / 1024).toFixed(0)} MB.`);
                if (status) status.textContent = 'El archivo supera el tamaño permitido.';
            } else {
                input.setCustomValidity('');
                if (status) status.textContent = 'Listo para subir.';
            }
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (!form.reportValidity()) return;
            const request = new XMLHttpRequest();
            request.open('POST', form.action);
            request.setRequestHeader('X-CSRF-TOKEN', csrf || '');
            request.setRequestHeader('Accept', 'application/json');
            if (progress) progress.classList.remove('hidden');
            if (status) status.textContent = 'Subiendo archivo…';
            if (bar) bar.style.width = '0%';
            request.upload.addEventListener('progress', (uploadEvent) => {
                if (!uploadEvent.lengthComputable) return;
                const percent = Math.round((uploadEvent.loaded / uploadEvent.total) * 100);
                if (bar) bar.style.width = `${percent}%`;
                if (status) status.textContent = `Subiendo archivo… ${percent}%`;
            });
            request.addEventListener('load', () => {
                if (request.status >= 200 && request.status < 400) {
                    if (status) status.textContent = 'Archivo subido correctamente. Actualizando…';
                    window.location.reload();
                } else if (status) {
                    status.textContent = 'No se pudo subir el archivo. Revisa el formato y el tamaño.';
                    if (progress) progress.classList.add('hidden');
                }
            });
            request.addEventListener('error', () => {
                if (status) status.textContent = 'Se perdió la conexión durante la carga.';
                if (progress) progress.classList.add('hidden');
            });
            request.send(new FormData(form));
        });
    });
});
