<?php

namespace App\Support;

use App\Models\Notification;

class NotificationService
{
    public const OPERATOR_BROADCAST_ID = 0;

    public static function currentUserId(?string $role = null): int
    {
        $role ??= session('role');

        if ($role === 'admin') {
            return (int) (session('pt') ?? session('user_id') ?? 0);
        }

        return (int) (session('user_id') ?? 0);
    }

    public static function actorUserId(): int
    {
        return (int) (session('user_id') ?? 0);
    }

    public static function visibleUserIds(?string $role = null): array
    {
        $role ??= session('role');
        $userId = self::currentUserId($role);

        if ($role === 'operator') {
            return array_values(array_unique([$userId, self::OPERATOR_BROADCAST_ID]));
        }

        if ($role === 'admin') {
            return array_values(array_unique([
                $userId,
                (int) (session('user_id') ?? 0),
            ]));
        }

        return [$userId];
    }

    public static function create(
        string $userType,
        int $userId,
        string $title,
        string $message,
        string $type = 'info',
        ?string $link = null
    ): Notification {
        return Notification::create([
            'user_type' => $userType,
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'is_read' => false,
        ]);
    }

    public static function notifyOperators(
        string $title,
        string $message,
        string $type = 'info',
        ?string $link = null
    ): Notification {
        return self::create('operator', self::OPERATOR_BROADCAST_ID, $title, $message, $type, $link);
    }

    public static function notifyAdminPt(
        int $idPt,
        string $title,
        string $message,
        string $type = 'info',
        ?string $link = null
    ): Notification {
        return self::create('admin', $idPt, $title, $message, $type, $link);
    }

    public static function queryForCurrentUser(?string $role = null)
    {
        $role ??= session('role');
        $ownerId = self::actorUserId();

        abort_unless($role && $ownerId, 403);

        return Notification::query()
            ->leftJoin('notification_user_states as personal_state', function ($join) use ($role, $ownerId) {
                $join->on('personal_state.notification_id', '=', 'notifications.id')
                    ->where('personal_state.owner_type', '=', $role)
                    ->where('personal_state.owner_id', '=', $ownerId);
            })
            ->where('notifications.user_type', $role)
            ->whereIn('notifications.user_id', self::visibleUserIds($role))
            ->whereNull('personal_state.deleted_at')
            ->select('notifications.*')
            ->selectRaw('COALESCE(personal_state.is_read, notifications.is_read) as is_read')
            ->selectRaw('personal_state.deleted_at as personal_deleted_at');
    }
}
