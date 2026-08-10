<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Notifications\NotificationLinkResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->user()->notifications()->latest();
        if ($request->string('filter')->toString() === 'unread') {
            $query->whereNull('read_at');
        }
        if ($request->string('filter')->toString() === 'mentions') {
            $query->whereJsonContains('data->event_type', 'mention');
        }

        return view('notifications.index', ['notifications' => $query->paginate(20)->withQueryString(), 'filter' => $request->string('filter')->toString() ?: 'all']);
    }

    public function read(Request $request, DatabaseNotification $notification, NotificationLinkResolver $links): RedirectResponse
    {
        $this->assertOwner($request->user(), $notification);
        $notification->markAsRead();

        return redirect($links->resolve($notification->data, $request->user()) ?? route('app.notifications'));
    }

    public function unread(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        $this->assertOwner($request->user(), $notification);
        $notification->markAsUnread();

        return back();
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('status', 'Notificaciones marcadas como leídas.');
    }

    private function assertOwner(User $user, DatabaseNotification $notification): void
    {
        abort_unless($notification->notifiable_type === $user->getMorphClass() && (int) $notification->notifiable_id === $user->id, 404);
    }
}
