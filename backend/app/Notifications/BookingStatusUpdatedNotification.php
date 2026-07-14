<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Booking $booking)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $statusText = match ($this->booking->status) {
            'dipesan' => 'Dipesan',
            'berjalan' => 'Berjalan',
            'selesai' => 'Selesai',
            'batal'   => 'Dibatalkan',
            default   => $this->booking->status,
        };

        return [
            'booking_id' => $this->booking->id,
            'status'     => $this->booking->status,
            'title'      => 'Status Booking Diperbarui',
            'message'    => "Booking kamu #{$this->booking->id} sekarang berstatus \"{$statusText}\".",
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
