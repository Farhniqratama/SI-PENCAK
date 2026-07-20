@extends('layouts.app')

@section('title', 'Semua Notifikasi')

@section('content')
    @php
        $totalNotifications = $notifications->total();
        $unreadTotal = $unreadTotal ?? $notifications->filter(fn ($notif) => ! $notif->is_read)->count();
    @endphp

    <div class="notifications-page">
        <div class="notifications-hero">
            <div>
                <span class="notifications-kicker">
                    <i class="ri-notification-3-line"></i>
                    Pusat Aktivitas
                </span>
                <h3 class="notifications-title">Riwayat Notifikasi</h3>
                <p class="notifications-subtitle">Pantau pembaruan pencairan, status verifikasi, dan aktivitas sistem.</p>
                <div class="notifications-toolbar">
                    <form action="{{ route('notifications.read-all') }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-primary" @disabled($unreadTotal === 0)>
                            <i class="ri-check-double-line me-1"></i> Read all
                        </button>
                    </form>
                    <form action="{{ route('notifications.delete-all') }}" method="post" onsubmit="return confirm('Hapus semua notifikasi dari akun ini?')">
                        @csrf
                        <button type="submit" class="btn btn-light border text-danger" @disabled($totalNotifications === 0)>
                            <i class="ri-delete-bin-line me-1"></i> Delete all
                        </button>
                    </form>
                </div>
            </div>
            <div class="notifications-summary">
                <div>
                    <span>Total</span>
                    <strong>{{ number_format($totalNotifications, 0, ',', '.') }}</strong>
                </div>
                <div>
                    <span>Belum dibaca</span>
                    <strong>{{ number_format($unreadTotal, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        @if($notifications->count() > 0)
            <div class="notifications-list">
                @foreach($notifications as $notif)
                    @php
                        $notifType = $notif->type ?: 'primary';
                        $notifIcon = match ($notifType) {
                            'success' => 'ri-checkbox-circle-line',
                            'danger' => 'ri-close-circle-line',
                            'warning' => 'ri-alert-line',
                            'info' => 'ri-information-line',
                            default => 'ri-notification-3-line',
                        };
                    @endphp

                    <article class="notification-row {{ $notif->is_read ? 'is-read' : 'is-unread' }}">
                        <div class="notification-icon notification-icon--{{ $notifType }}">
                            <i class="{{ $notifIcon }}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-row-head">
                                <div>
                                    <h5>{{ $notif->title }}</h5>
                                    <span class="notification-state">{{ $notif->is_read ? 'Sudah dibaca' : 'Belum dibaca' }}</span>
                                </div>
                                <div class="notification-row-tools">
                                    <time>
                                        <i class="ri-time-line"></i>
                                        {{ $notif->created_at->format('d M Y H:i') }}
                                    </time>
                                    <form action="{{ route('notifications.delete', $notif->id) }}" method="post" onsubmit="return confirm('Hapus notifikasi ini dari akun ini?')">
                                        @csrf
                                        <button type="submit" class="notification-delete-btn" title="Hapus notifikasi">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <p>{{ $notif->message }}</p>
                            <div class="notification-actions">
                                <span>{{ $notif->created_at->diffForHumans() }}</span>
                                @if($notif->link)
                                    <a href="{{ route('notifications.open', $notif->id) }}" class="btn btn-sm btn-primary">
                                        Lihat Detail <i class="ri-arrow-right-line ms-1"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="notifications-pager">
                <div class="text-muted fw-bold">
                    Menampilkan {{ $notifications->firstItem() }}-{{ $notifications->lastItem() }} dari total {{ $notifications->total() }} Notifikasi
                </div>
                {{ $notifications->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="notifications-empty">
                <i class="ri-notification-off-line"></i>
                <h4>Tidak Ada Notifikasi</h4>
                <p>Belum ada aktivitas atau pemberitahuan baru untuk akun ini.</p>
            </div>
        @endif
    </div>
@endsection
