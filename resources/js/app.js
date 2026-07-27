import './bootstrap';

import Alpine from 'alpinejs';
import {
    Activity,
    AlarmClock,
    ArrowDown,
    ArrowRight,
    ArrowUp,
    Ban,
    Bell,
    Box,
    Check,
    CheckCheck,
    CheckCircle,
    ChevronDown,
    Circle,
    CircleCheck,
    CircleX,
    Clock3,
    Download,
    Eye,
    FileEdit,
    FileText,
    Gauge,
    Inbox,
    Info,
    Layers3,
    LayoutDashboard,
    LoaderCircle,
    LockKeyhole,
    Maximize2,
    Menu,
    MessageCircle,
    Minus,
    MoreHorizontal,
    PauseCircle,
    Paperclip,
    PenTool,
    PlayCircle,
    RefreshCw,
    ScanSearch,
    Search,
    SearchCheck,
    TextCursorInput,
    TriangleAlert,
    UploadCloud,
    UserCheck,
    Video,
    X,
    createIcons,
} from 'lucide';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons: {
        Activity, AlarmClock, ArrowDown, ArrowRight, ArrowUp, Ban, Bell, Box, Check, CheckCheck,
        CheckCircle, ChevronDown, Circle, CircleCheck, CircleX, Clock3, Download, Eye, FileEdit,
        FileText, Gauge, Inbox, Info, Layers3, LayoutDashboard, LoaderCircle, LockKeyhole,
        Maximize2, Menu, MessageCircle, Minus, MoreHorizontal, Paperclip, PauseCircle, PenTool, PlayCircle, RefreshCw,
        ScanSearch, Search, SearchCheck, TextCursorInput, TriangleAlert, UploadCloud, UserCheck,
        Video, X,
    } });
});
