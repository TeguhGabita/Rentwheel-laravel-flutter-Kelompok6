import 'package:flutter/material.dart';
import '../../services/api_service.dart';

class RiwayatBookingScreen extends StatefulWidget {
  const RiwayatBookingScreen({super.key});

  @override
  State<RiwayatBookingScreen> createState() => _RiwayatBookingScreenState();
}

class _RiwayatBookingScreenState extends State<RiwayatBookingScreen> {
  // ==================== PALET WARNA (sama dengan layar lain) ====================
  static const Color navyDark = Color(0xFF14213D);
  static const Color navyLight = Color(0xFF223159);
  static const Color amber = Color(0xFFFFB703);
  static const Color orange = Color(0xFFFB8500);
  static const Color teal = Color(0xFF2EC4B6);
  static const Color bgTop = Color(0xFFFFF6EA);
  static const Color bgBottom = Color(0xFFE9F3F4);

  List bookings = [];
  bool loading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      loading = true;
      errorMessage = null;
    });

    final result = await ApiService.getBookings();

    if (!mounted) return;

    if (result["success"] == true) {
      final data = result["data"];
      // TODO: sesuaikan kalau list booking ternyata dibungkus di key lain,
      // misal {"data": {"bookings": [...]}} bukan langsung List.
      final list = data is List ? data : (data is Map ? (data["data"] ?? data["bookings"] ?? []) : []);
      setState(() {
        bookings = list is List ? list : [];
        loading = false;
      });
    } else {
      setState(() {
        loading = false;
        errorMessage = result["message"] ?? "Gagal memuat riwayat booking.";
      });
    }
  }

  String _rupiah(num value) {
    final str = value.toInt().toString();
    final buffer = StringBuffer();
    for (int i = 0; i < str.length; i++) {
      final posFromRight = str.length - i;
      buffer.write(str[i]);
      if (posFromRight > 1 && posFromRight % 3 == 1) buffer.write('.');
    }
    return 'Rp ${buffer.toString()}';
  }

  String? _mobilNama(dynamic item) {
    if (item['mobil'] is Map) return item['mobil']['nama_mobil']?.toString();
    return item['nama_mobil']?.toString();
  }

  String? _mobilMerk(dynamic item) {
    if (item['mobil'] is Map) return item['mobil']['merk']?.toString();
    return item['merk']?.toString();
  }

  Color _statusColor(String status) {
    switch (status.toLowerCase()) {
      case 'selesai':
      case 'dikonfirmasi':
      case 'lunas':
        return const Color(0xFF06A77D);
      case 'berjalan':
        return amber;
      case 'dipesan':
        return Colors.blue;
      case 'menunggu pembayaran':
      case 'pending':
        return orange;
      case 'dibatalkan':
        return const Color(0xFFEF476F);
      default:
        return Colors.grey;
    }
  }

  IconData _statusIcon(String status) {
    switch (status.toLowerCase()) {
      case 'selesai':
      case 'dikonfirmasi':
      case 'lunas':
        return Icons.check_circle_rounded;
      case 'berjalan':
        return Icons.directions_car_filled_rounded;
      case 'dipesan':
        return Icons.event_available_rounded;
      case 'menunggu pembayaran':
      case 'pending':
        return Icons.hourglass_top_rounded;
      case 'dibatalkan':
        return Icons.cancel_rounded;
      default:
        return Icons.receipt_long_rounded;
    }
  }

  Widget _emptyState() {
    return ListView(
      children: [
        const SizedBox(height: 90),
        Center(
          child: Container(
            width: 72,
            height: 72,
            decoration: BoxDecoration(
              color: navyLight.withValues(alpha: 0.08),
              shape: BoxShape.circle,
            ),
            child: Icon(Icons.receipt_long_outlined, color: navyLight.withValues(alpha: 0.5), size: 32),
          ),
        ),
        const SizedBox(height: 14),
        Center(
          child: Text(
            'Belum ada riwayat booking',
            style: TextStyle(color: Colors.grey.shade600, fontWeight: FontWeight.w600, fontSize: 13.5),
          ),
        ),
        const SizedBox(height: 4),
        Center(
          child: Text(
            'Booking mobil pertamamu akan muncul di sini',
            style: TextStyle(color: Colors.grey.shade500, fontSize: 12),
          ),
        ),
      ],
    );
  }

  Widget _errorState() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 64,
              height: 64,
              decoration: BoxDecoration(
                color: const Color(0xFFEF476F).withValues(alpha: 0.1),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.error_outline_rounded, color: Color(0xFFEF476F), size: 30),
            ),
            const SizedBox(height: 14),
            Text(
              errorMessage!,
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey.shade700, fontSize: 13),
            ),
            const SizedBox(height: 14),
            Material(
              color: orange.withValues(alpha: 0.12),
              borderRadius: BorderRadius.circular(10),
              child: InkWell(
                borderRadius: BorderRadius.circular(10),
                onTap: _load,
                child: const Padding(
                  padding: EdgeInsets.symmetric(horizontal: 18, vertical: 10),
                  child: Text('Coba lagi',
                      style: TextStyle(color: orange, fontWeight: FontWeight.w700, fontSize: 13)),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _bookingCard(dynamic item) {
    final status = (item['status'] ?? '-').toString();
    final statusColor = _statusColor(status);
    final merk = _mobilMerk(item);
    final harga = num.tryParse((item['total_harga'] ?? item['total_bayar'])?.toString() ?? '') ;

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: navyDark.withValues(alpha: 0.06),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 42,
                  height: 42,
                  decoration: BoxDecoration(
                    color: teal.withValues(alpha: 0.12),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(Icons.directions_car_rounded, color: teal, size: 20),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        _mobilNama(item) ?? 'Mobil',
                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14.5, color: navyDark),
                      ),
                      if (merk != null && merk.isNotEmpty)
                        Padding(
                          padding: const EdgeInsets.only(top: 2),
                          child: Text(merk, style: TextStyle(fontSize: 11.5, color: Colors.grey.shade500)),
                        ),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 5),
                  decoration: BoxDecoration(
                    color: statusColor.withValues(alpha: 0.12),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(_statusIcon(status), size: 11, color: statusColor),
                      const SizedBox(width: 4),
                      Text(
                        status,
                        style: TextStyle(fontSize: 10.5, color: statusColor, fontWeight: FontWeight.w700),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            Container(height: 1, color: Colors.grey.shade100),
            const SizedBox(height: 12),
            Row(
              children: [
                Icon(Icons.calendar_today_rounded, size: 13, color: Colors.grey.shade500),
                const SizedBox(width: 6),
                Text(
                  '${item['tanggal_mulai'] ?? '-'}',
                  style: TextStyle(fontSize: 12, color: Colors.grey.shade700, fontWeight: FontWeight.w500),
                ),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 6),
                  child: Icon(Icons.arrow_forward_rounded, size: 12, color: Colors.grey.shade400),
                ),
                Text(
                  '${item['tanggal_selesai'] ?? '-'}',
                  style: TextStyle(fontSize: 12, color: Colors.grey.shade700, fontWeight: FontWeight.w500),
                ),
              ],
            ),
            if (harga != null) ...[
              const SizedBox(height: 8),
              Row(
                children: [
                  const Icon(Icons.payments_rounded, size: 13, color: orange),
                  const SizedBox(width: 6),
                  Text(
                    _rupiah(harga),
                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13.5, color: orange),
                  ),
                ],
              ),
            ],
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: bgTop,
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [bgTop, bgBottom],
          ),
        ),
        child: SafeArea(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Padding(
                padding: const EdgeInsets.fromLTRB(20, 20, 20, 8),
                child: Row(
                  children: [
                    Container(
                      width: 34,
                      height: 34,
                      decoration: BoxDecoration(
                        color: navyDark,
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: const Icon(Icons.receipt_long_rounded, color: amber, size: 17),
                    ),
                    const SizedBox(width: 10),
                    const Text(
                      'Riwayat Booking',
                      style: TextStyle(fontSize: 19, fontWeight: FontWeight.bold, color: navyDark),
                    ),
                  ],
                ),
              ),
              if (!loading && errorMessage == null && bookings.isNotEmpty)
                Padding(
                  padding: const EdgeInsets.fromLTRB(20, 4, 20, 4),
                  child: Text(
                    '${bookings.length} booking ditemukan',
                    style: TextStyle(fontSize: 12, color: Colors.grey.shade500),
                  ),
                ),
              Expanded(
                child: loading
                    ? const Center(child: CircularProgressIndicator(color: orange))
                    : errorMessage != null
                        ? _errorState()
                        : RefreshIndicator(
                            color: orange,
                            onRefresh: _load,
                            child: bookings.isEmpty
                                ? _emptyState()
                                : ListView.builder(
                                    padding: const EdgeInsets.fromLTRB(16, 6, 16, 16),
                                    itemCount: bookings.length,
                                    itemBuilder: (context, index) => _bookingCard(bookings[index]),
                                  ),
                          ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}