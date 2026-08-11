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
    createIcons({ icons: {
        Activity, AlarmClock, ArrowDown, ArrowLeft, ArrowRight, ArrowUp, Ban, Bell, Box,
        BriefcaseBusiness, Check, CheckCheck, CheckCircle, ChevronDown, Circle, CircleCheck,
        CircleX, Clock3, Download, Eye, EyeOff, FileEdit, FileText, Gauge, Inbox, Info,
        Layers3, LayoutDashboard, LoaderCircle, LockKeyhole, Mail, Maximize2, Menu,
        LogOut, MessageCircle, Minus, MoreHorizontal, Palette, Paperclip, PauseCircle, PenTool, Plus,
        PlayCircle, RefreshCw, ScanSearch, Search, SearchCheck, ShieldCheck, TextCursorInput,
        TriangleAlert, UploadCloud, UserCheck, Video, X,
    } });

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

    document.querySelectorAll('form.js-upload-form').forEach((form) => {
        const input = form.querySelector('input[type="file"]');
        const progress = form.querySelector('[data-upload-progress]');
        const bar = form.querySelector('[data-upload-progress-bar]');
        const status = form.querySelector('[data-upload-status]');
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
