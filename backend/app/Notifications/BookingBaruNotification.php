<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingBaruNotification extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'pelanggan'  => $this->booking->user->name ?? 'User',
            'mobil'      => $this->booking->mobil->nama_mobil ?? '-',
            'total'      => $this->booking->total_harga,
            'message'    => 'Booking baru dari ' . ($this->booking->user->name ?? 'User') . ' untuk mobil ' . ($this->booking->mobil->nama_mobil ?? '-'),
            'url'        => route('admin.laporan.index'),
        ];
    }
}
