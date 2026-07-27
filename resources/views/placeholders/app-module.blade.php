@extends('layouts.app')
@section('title', 'Módulo en construcción')
@section('header', 'Módulo en construcción')
@section('content')
<div class="mx-auto flex min-h-[60vh] max-w-2xl items-center justify-center"><x-ui.empty-state title="Módulo en construcción" description="Esta experiencia estará disponible en una fase posterior. Tu sesión y permisos siguen protegidos." icon="layers-3"><a href="{{ route('app.dashboard') }}" class="inline-flex min-h-11 items-center gap-2 rounded-[var(--radius-md)] bg-[var(--color-action-primary)] px-4 text-sm font-semibold"><i data-lucide="arrow-left" class="h-4 w-4" aria-hidden="true"></i>Volver al dashboard</a></x-ui.empty-state></div>
@endsection
