@props(['service'])
<a href="{{ route('app.requests.create', ['service' => $service['key']]) }}" class="group block h-full rounded-2xl border border-slate-800 bg-slate-900/90 p-6 shadow-lg hover:border-red-500/60 hover:bg-slate-900 hover:shadow-red-900/20 transition cursor-pointer flex flex-col justify-between">
    <div>
        <div class="flex items-start justify-between gap-3">
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-slate-800 text-slate-300 group-hover:scale-110 group-hover:bg-red-600/20 group-hover:text-red-400 transition">
                <i data-lucide="{{ $service['icon'] }}" class="h-5 w-5" aria-hidden="true"></i>
            </span>
            <span class="rounded bg-emerald-500/20 border border-emerald-500/30 px-2.5 py-0.5 text-xs font-bold text-emerald-300">
                Disponible
            </span>
        </div>
        <h3 class="mt-5 font-bold text-white text-base group-hover:text-red-400 transition">
            {{ $service['name'] }}
        </h3>
        <p class="mt-2 text-xs leading-5 text-slate-400">
            {{ $service['description'] }}
        </p>
    </div>
    <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs font-extrabold text-red-500 group-hover:text-red-400 transition">
        <span>Crear solicitud</span>
        <span class="group-hover:translate-x-1 transition">→</span>
    </div>
</a>
