@extends('layouts.admin')
@section('title', 'Crear usuario · TG Creative Hub')
@section('header', 'Crear usuario')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <!-- Top Navigation & Title Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-800 bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 p-6 shadow-xl">
        <div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition mb-2">
                <span>←</span>
                <span>Volver al Directorio de Usuarios</span>
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-2">
                <span>➕</span>
                <span>Crear Nuevo Usuario</span>
            </h1>
            <p class="mt-1 text-sm text-slate-400">
                Registra a un nuevo integrante del equipo asignándole un rol y departamento.
            </p>
        </div>
    </div>

    <!-- Create Form Card -->
    <form method="POST" action="{{ route('admin.users.store') }}" class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 sm:p-8 shadow-2xl space-y-8">
        @csrf

        @include('admin.users.form', ['managedUser' => null])

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-slate-800">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-800 hover:bg-slate-700 px-5 py-3 text-xs font-bold text-slate-300 transition">
                Cancelar
            </a>

            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 px-7 py-3 text-sm font-extrabold text-white transition shadow-lg shadow-red-900/30">
                <span>✨</span>
                <span>Crear Usuario</span>
            </button>
        </div>
    </form>
</div>
@endsection
