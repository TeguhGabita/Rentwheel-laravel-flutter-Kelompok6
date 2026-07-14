@php
    $notifikasi = auth()->user()->unreadNotifications;
    $jumlahNotif = $notifikasi->count();
@endphp

<div class="dropdown">
    <a href="#" class="position-relative text-white" data-bs-toggle="dropdown">
        <i class="bi bi-bell fs-5"></i>
        @if($jumlahNotif > 0)
            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-circle">
                {{ $jumlahNotif }}
            </span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end p-2" style="width:320px; max-height:350px; overflow-y:auto;">
        @forelse($notifikasi as $notif)
            <a href="{{ route('notifikasi.baca', $notif->id) }}" class="dropdown-item small border-bottom py-2">
                {{ $notif->data['message'] }}
                <br><span class="text-muted" style="font-size:11px;">{{ $notif->created_at->diffForHumans() }}</span>
            </a>
        @empty
            <span class="dropdown-item text-muted">Tidak ada notifikasi</span>
        @endforelse
    </div>
</div>
