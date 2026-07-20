<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationUserState extends Model
{
    protected $fillable = [
        'notification_id',
        'owner_type',
        'owner_id',
        'is_read',
        'deleted_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'deleted_at' => 'datetime',
    ];
}
