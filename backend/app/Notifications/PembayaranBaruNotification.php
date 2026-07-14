<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PembayaranBaruNotification extends Notification
{
    use Queueable;

    protected $pembayaran;

    public function __construct($pembayaran)
    {
        $this->pembayaran = $pembayaran;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $booking = $this->pembayaran->booking;

        return [
            'pembayaran_id' => $this->pembayaran->id,
            'booking_id'    => $booking->id ?? null,
            'pelanggan'     => $booking->user->name ?? 'User',
            'mobil'         => $booking->mobil->nama_mobil ?? '-',
            'jumlah_bayar'  => $this->pembayaran->jumlah_bayar,
            'message'       => 'Pembayaran baru dari ' . ($booking->user->name ?? 'User') . ' sebesar Rp ' . number_format($this->pembayaran->jumlah_bayar, 0, ',', '.') . ' menunggu verifikasi',
            'url'           => route('admin.pembayaran.index'),
        ];
    }
}
