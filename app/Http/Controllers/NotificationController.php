<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationUserState;
use App\Support\NotificationService;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = $this->queryForCurrentUser()
            ->orderBy('notifications.created_at', 'desc')
            ->paginate(20);

        $unreadTotal = $this->queryForCurrentUser()
            ->whereRaw('COALESCE(personal_state.is_read, notifications.is_read) = 0')
            ->count();

        return view('notifications', compact('notifications', 'unreadTotal'));
    }

    public function open(Notification $notification)
    {
        $this->ensureVisible($notification);

        $this->upsertState($notification->id, [
            'is_read' => true,
            'deleted_at' => null,
        ]);

        return redirect($notification->link ?: $this->indexUrl());
    }

    public function markAllRead()
    {
        $this->queryForCurrentUser()
            ->whereRaw('COALESCE(personal_state.is_read, notifications.is_read) = 0')
            ->pluck('notifications.id')
            ->each(fn ($id) => $this->upsertState((int) $id, [
                'is_read' => true,
                'deleted_at' => null,
            ]));

        return back();
    }

    public function destroy(Notification $notification)
    {
        $this->ensureVisible($notification);

        $this->upsertState($notification->id, [
            'is_read' => true,
            'deleted_at' => Carbon::now(),
        ]);

        return back();
    }

    public function destroyAll()
    {
        $deletedAt = Carbon::now();

        $this->queryForCurrentUser()
            ->pluck('notifications.id')
            ->each(fn ($id) => $this->upsertState((int) $id, [
                'is_read' => true,
                'deleted_at' => $deletedAt,
            ]));

        return back();
    }

    private function queryForCurrentUser()
    {
        return NotificationService::queryForCurrentUser();
    }

    private function ensureVisible(Notification $notification): void
    {
        abort_unless(
            $this->queryForCurrentUser()
                ->where('notifications.id', $notification->id)
                ->exists(),
            403
        );
    }

    private function indexUrl(): string
    {
        return url(session('role') === 'operator' ? 'operator/notifikasi' : 'admin/notifikasi');
    }

    private function upsertState(int $notificationId, array $values): NotificationUserState
    {
        return NotificationUserState::updateOrCreate(
            [
                'notification_id' => $notificationId,
                'owner_type' => session('role'),
                'owner_id' => NotificationService::actorUserId(),
            ],
            $values
        );
    }
}
