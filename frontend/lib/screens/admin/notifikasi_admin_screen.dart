import 'package:flutter/material.dart';
import '../../services/api_service.dart'; // sesuaikan path relatif dengan struktur folder kamu

/// PENTING: backend pakai Laravel Database Notification (`$user->notifications()`),
/// jadi setiap item punya bentuk:
///   { "id": "...", "data": { ...isi notifikasi asli... }, "read_at": null atau timestamp, "created_at": "..." }
/// Bukan field rata (flat) seperti { "title": ..., "message": ..., "is_read": ... }.
/// Makanya semua akses field pesan/judul/type/reference_id harus lewat notif['data'][...],
/// dan status baca dicek dari notif['read_at'] (null = belum dibaca).
class NotifikasiScreen extends StatefulWidget {
  const NotifikasiScreen({super.key});

  @override
  State<NotifikasiScreen> createState() => _NotifikasiScreenState();
}

class _NotifikasiScreenState extends State<NotifikasiScreen> {
  List<dynamic> _notifikasi = [];
  bool _loading = true;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _loadNotifikasi();
  }

  Future<void> _loadNotifikasi() async {
    setState(() {
      _loading = true;
      _errorMessage = null;
    });

    final result = await ApiService.getNotifikasi();

    if (!mounted) return;

    if (result['success'] == true) {
      setState(() {
        _notifikasi = result['data'] as List<dynamic>;
        _loading = false;
      });
    } else {
      setState(() {
        _errorMessage = result['message'] ?? 'Gagal memuat notifikasi.';
        _loading = false;
      });
    }
  }

  /// Ambil isi data notifikasi (nested) dengan aman.
  Map<String, dynamic> _dataOf(dynamic notif) {
    final data = notif['data'];
    if (data is Map) return Map<String, dynamic>.from(data);
    return {};
  }

  /// Judul ditentukan dari nama class notifikasi Laravel (field 'type' di level atas,
  /// diisi controller dari class_basename($notif->type)), karena backend tidak
  /// mengirim field 'title' terpisah di dalam data.
  String _judulOf(dynamic notif) {
    switch (notif['type']) {
      case 'BookingBaruNotification':
        return 'Booking Baru';
      case 'BookingDikonfirmasiNotification':
        return 'Booking Dikonfirmasi';
      case 'PembayaranBaruNotification':
        return 'Pembayaran Baru';
      default:
        return 'Notifikasi';
    }
  }

  /// Pesan selalu ada di data['message'] untuk ketiga jenis notifikasi.
  String _pesanOf(Map<String, dynamic> data) {
    return (data['message'] ?? '').toString();
  }

  dynamic _referenceIdOf(dynamic notif, Map<String, dynamic> data) {
    switch (notif['type']) {
      case 'PembayaranBaruNotification':
        return data['pembayaran_id'];
      default:
        return data['booking_id'];
    }
  }

  Future<void> _tandaiSudahDibaca(dynamic notif) async {
    final data = _dataOf(notif);

    // read_at null artinya belum dibaca (struktur Laravel Database Notification)
    if (notif['read_at'] != null) {
      _bukaDetail(notif, data);
      return;
    }

    final result = await ApiService.bacaNotifikasi(notif['id']);

    if (!mounted) return;

    if (result['success'] == true) {
      setState(() {
        notif['read_at'] = DateTime.now().toIso8601String();
      });
      _bukaDetail(notif, data);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? 'Gagal menandai notifikasi.')),
      );
    }
  }

  void _bukaDetail(dynamic notif, Map<String, dynamic> data) {
    // Arahkan sesuai type notifikasi, misal ke detail booking/pembayaran
    final type = notif['type'];
    final referenceId = _referenceIdOf(notif, data);

    if (type == 'BookingBaruNotification' || type == 'BookingDikonfirmasiNotification') {
      Navigator.pushNamed(context, '/booking-detail', arguments: referenceId);
    } else if (type == 'PembayaranBaruNotification') {
      Navigator.pushNamed(context, '/pembayaran-detail', arguments: referenceId);
    }
    // kalau type lain, cukup tampilkan tanpa navigasi
  }

  Future<void> _tandaiSemuaSudahDibaca() async {
    final result = await ApiService.bacaSemuaNotifikasi();

    if (!mounted) return;

    if (result['success'] == true) {
      setState(() {
        for (var notif in _notifikasi) {
          notif['read_at'] = DateTime.now().toIso8601String();
        }
      });
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? 'Gagal menandai semua notifikasi.')),
      );
    }
  }

  IconData _iconForType(String? type) {
    switch (type) {
      case 'BookingBaruNotification':
        return Icons.event_note;
      case 'PembayaranBaruNotification':
        return Icons.payments_outlined;
      case 'BookingDikonfirmasiNotification':
        return Icons.check_circle_outline;
      default:
        return Icons.notifications_outlined;
    }
  }

  @override
  Widget build(BuildContext context) {
    final adaBelumDibaca = _notifikasi.any((n) => n['read_at'] == null);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Notifikasi'),
        actions: [
          if (adaBelumDibaca)
            TextButton(
              onPressed: _tandaiSemuaSudahDibaca,
              child: const Text('Tandai semua dibaca', style: TextStyle(color: Colors.white)),
            ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _loadNotifikasi,
        child: _buildBody(),
      ),
    );
  }

  Widget _buildBody() {
    if (_loading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_errorMessage != null) {
      return ListView(
        // ListView supaya RefreshIndicator tetap bisa ditarik walau error
        children: [
          const SizedBox(height: 100),
          Center(
            child: Column(
              children: [
                Text(_errorMessage!, textAlign: TextAlign.center),
                const SizedBox(height: 12),
                ElevatedButton(onPressed: _loadNotifikasi, child: const Text('Coba Lagi')),
              ],
            ),
          ),
        ],
      );
    }

    if (_notifikasi.isEmpty) {
      return ListView(
        children: const [
          SizedBox(height: 100),
          Center(child: Text('Belum ada notifikasi.')),
        ],
      );
    }

    return ListView.separated(
      itemCount: _notifikasi.length,
      separatorBuilder: (_, __) => const Divider(height: 1),
      itemBuilder: (context, index) {
        final notif = _notifikasi[index];
        final data = _dataOf(notif);
        final bool isRead = notif['read_at'] != null;

        return ListTile(
          onTap: () => _tandaiSudahDibaca(notif),
          leading: CircleAvatar(
            backgroundColor: isRead ? Colors.grey[300] : Colors.orange[100],
            child: Icon(
              _iconForType(notif['type']?.toString()),
              color: isRead ? Colors.grey : Colors.orange[800],
            ),
          ),
          title: Text(
            _judulOf(notif),
            style: TextStyle(fontWeight: isRead ? FontWeight.normal : FontWeight.bold),
          ),
          subtitle: Text(_pesanOf(data)),
          trailing: isRead
              ? null
              : Container(
                  width: 10,
                  height: 10,
                  decoration: const BoxDecoration(color: Colors.red, shape: BoxShape.circle),
                ),
        );
      },
    );
  }
}