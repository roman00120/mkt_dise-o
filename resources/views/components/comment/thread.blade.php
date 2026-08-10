@props(['commentable', 'comments', 'internal' => false, 'storeRoute', 'replyRoute', 'routeParameters' => null])
@php($routeParameters = $routeParameters ?? [$commentable])
<section class="rounded-[var(--radius-card)] border {{ $internal ? 'border-amber-500/40 bg-amber-500/5' : 'border-[var(--color-border-subtle)] bg-[var(--color-surface-default)]' }} p-5" aria-labelledby="{{ $internal ? 'internal-notes-heading' : 'conversation-heading' }}">
    <div class="flex items-start justify-between gap-3"><div><h2 id="{{ $internal ? 'internal-notes-heading' : 'conversation-heading' }}" class="text-lg font-semibold">{{ $internal ? 'Notas internas' : 'Conversación' }}</h2><p class="mt-1 text-sm text-[var(--color-text-secondary)]">{{ $internal ? 'Solo visible para el equipo creativo.' : 'Comentarios públicos de la solicitud.' }}</p></div><span class="rounded-full border px-2 py-1 text-xs">{{ $internal ? 'Interno' : 'Público' }}</span></div>
    <div class="mt-5 space-y-4">
        @forelse ($comments as $comment)
            <article id="comment-{{ $comment->uuid }}" class="rounded border border-[var(--color-border-subtle)] p-4">
                @if ($comment->trashed())<p class="text-sm italic text-[var(--color-text-tertiary)]">Comentario eliminado</p>@else
                    <div class="flex items-start justify-between gap-3"><div><p class="font-semibold">{{ $comment->author->name }}</p><time class="text-xs text-[var(--color-text-tertiary)]" datetime="{{ $comment->created_at?->toIso8601String() }}">{{ $comment->created_at?->diffForHumans() }} · {{ $comment->created_at?->isoFormat('D MMM YYYY HH:mm') }}</time></div>@if($comment->edited_at)<span class="text-xs text-[var(--color-text-tertiary)]">Editado</span>@endif</div>
                    <p class="mt-3 whitespace-pre-line text-sm">{{ $comment->body }}</p>
                    @if($comment->replies->isNotEmpty())<div class="mt-4 space-y-3 border-l-2 border-[var(--color-action-primary)] pl-4">@foreach($comment->replies as $reply)<div id="comment-{{ $reply->uuid }}"><p class="text-sm font-semibold">{{ $reply->author->name }}</p><p class="mt-1 whitespace-pre-line text-sm">{{ $reply->body }}</p></div>@endforeach</div>@endif
                @endif
            </article>
        @empty
            <p class="rounded border border-dashed border-[var(--color-border-default)] p-6 text-sm text-[var(--color-text-secondary)]">{{ $internal ? 'No hay notas internas.' : 'Aún no hay comentarios.' }}</p>
        @endforelse
    </div>
    <form method="POST" action="{{ route($storeRoute, $routeParameters) }}" enctype="multipart/form-data" class="mt-5 border-t border-[var(--color-border-subtle)] pt-5">@csrf<label class="block text-sm font-semibold">{{ $internal ? 'Agregar nota interna' : 'Añadir comentario' }}<textarea name="body" required maxlength="5000" rows="4" class="mt-2 w-full rounded border border-[var(--color-border-default)] bg-[var(--color-bg-primary)] p-3 text-sm"></textarea></label><input type="file" name="attachments[]" multiple class="mt-3 block w-full text-sm"><button class="mt-3 min-h-11 rounded bg-[var(--color-action-primary)] px-4 text-sm font-semibold">{{ $internal ? 'Guardar nota interna' : 'Publicar comentario' }}</button></form>
</section>
