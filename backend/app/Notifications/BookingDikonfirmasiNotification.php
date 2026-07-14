<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingDikonfirmasiNotification extends Notification
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
            'mobil'      => $this->booking->mobil->nama_mobil ?? '-',
            'status'     => $this->booking->status,
            'message'    => 'Status booking mobil ' . ($this->booking->mobil->nama_mobil ?? '') . ' kamu diperbarui menjadi "' . $this->booking->status . '"',
            'url'        => route('booking.show', $this->booking->id),
        ];
    }
}
