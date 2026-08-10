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
});
