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
});
