<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Ambil daftar notifikasi milik user yang sedang login.
     * Mengembalikan semua notifikasi (dibaca & belum), diurutkan terbaru dulu,
     * plus jumlah yang belum dibaca supaya badge di lonceng bisa ditampilkan.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $notifikasi = $user->notifications()
            ->latest()
            ->limit(30)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'type' => class_basename($notif->type),
                    'data' => $notif->data,
                    'read_at' => $notif->read_at,
                    'created_at' => $notif->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $notifikasi,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(Request $request, $id)
    {
        $notif = $request->user()->notifications()->findOrFail($id);
        $notif->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua notifikasi milik user yang login sebagai sudah dibaca.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
