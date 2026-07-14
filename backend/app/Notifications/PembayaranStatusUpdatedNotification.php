<?php

namespace App\Notifications;

use App\Models\Pembayaran;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PembayaranStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Pembayaran $pembayaran)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $statusText = match ($this->pembayaran->status_bayar) {
            'pending' => 'Menunggu Konfirmasi',
            'lunas'   => 'Lunas',
            'ditolak' => 'Ditolak',
            default   => $this->pembayaran->status_bayar,
        };

        return [
            'pembayaran_id' => $this->pembayaran->id,
            'booking_id'    => $this->pembayaran->booking_id,
            'status_bayar'  => $this->pembayaran->status_bayar,
            'title'         => 'Status Pembayaran Diperbarui',
            'message'       => "Pembayaran untuk booking #{$this->pembayaran->booking_id} sekarang berstatus \"{$statusText}\".",
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
