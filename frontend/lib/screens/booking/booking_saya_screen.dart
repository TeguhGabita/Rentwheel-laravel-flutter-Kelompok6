import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../pembayaran/pembayaran_screen.dart';

class BookingSayaScreen extends StatefulWidget {
  const BookingSayaScreen({super.key});

  @override
  State<BookingSayaScreen> createState() => _BookingSayaScreenState();
}

class _BookingSayaScreenState extends State<BookingSayaScreen> {
  // ==================== PALET WARNA (sama dengan HomeScreen) ====================
  static const Color navyDark = Color(0xFF14213D);
  static const Color navyLight = Color(0xFF223159);
  static const Color amber = Color(0xFFFFB703);
  static const Color orange = Color(0xFFFB8500);
  static const Color teal = Color(0xFF2EC4B6);
  static const Color bgTop = Color(0xFFFFF6EA);
  static const Color bgBottom = Color(0xFFE9F3F4);

  List bookings = [];
  bool loading = true;
  String? error;

  @override
  void initState() {
    super.initState();
    _loadBookings();
  }

  Future<void> _loadBookings() async {
    setState(() {
      loading = true;
      error = null;
    });

    final result = await ApiService.getBookings();

    if (!mounted) return;

    if (result['success'] == true) {
      final raw = result['data'];
      final List data = raw is List
          ? raw
          : (raw is Map && raw['data'] is List ? raw['data'] : []);
      setState(() {
        bookings = data;
        loading = false;
      });
    } else {
      setState(() {
        error = result['message']?.toString() ?? 'Gagal memuat data booking.';
        loading = false;
      });
    }
  }

  // ---------- HELPER: field booking, fleksibel terhadap variasi nama key ----------
  String _namaMobil(dynamic b) {
    if (b['mobil'] is Map) {
      return (b['mobil']['nama_mobil'] ?? b['mobil']['nama'] ?? '-').toString();
    }
    return (b['nama_mobil'] ?? '-').toString();
  }

  num _totalHarga(dynamic b) {
    final raw = b['total_harga'] ?? b['total_bayar'];
    if (raw == null) return 0;
    return num.tryParse(raw.toString()) ?? 0;
  }

  String _statusBooking(dynamic b) => (b['status'] ?? '-').toString();

  // FIX: 'pembayaran' adalah relasi hasOne (Map tunggal / null di JSON),
  // field statusnya nested di 'status_bayar', bukan flat 'status_pembayaran'.
  // Nilai enum di DB: 'pending', 'lunas', 'gagal' (default 'pending').
  String _statusPembayaran(dynamic b) {
    final pembayaran = b['pembayaran'];
    if (pembayaran is Map) {
      return (pembayaran['status_bayar'] ?? '').toString().toLowerCase();
    }
    return '';
  }

  bool _belumBayar(dynamic b) {
    final s = _statusPembayaran(b);
    final statusBooking = _statusBooking(b).toLowerCase();

    // Safety net: booking yang sudah selesai/batal jangan pernah nampilin
    // tombol "Bayar Sekarang", walau data status_bayar kosong/aneh.
    if (statusBooking == 'selesai' || statusBooking == 'batal') return false;

    // 'pending', 'gagal', atau kosong (belum ada record pembayaran) → belum bayar
    return s != 'lunas';
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

  // FIX: nilai status booking yang benar adalah 'dipesan', 'berjalan',
  // 'selesai', 'batal' (bukan 'aktif', 'berlangsung', 'dikonfirmasi', 'dibatalkan').
  Color _statusColor(String status) {
    switch (status.toLowerCase()) {
      case 'dipesan':
      case 'berjalan':
        return teal;
      case 'selesai':
        return const Color(0xFF06A77D);
      case 'batal':
        return const Color(0xFFEF476F);
      default:
        return navyLight;
    }
  }

  void _bayarSekarang(dynamic b) {
    final id = b['id'];
    if (id == null) return;

    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => PembayaranScreen(
          bookingId: id is int ? id : int.parse(id.toString()),
          totalBayar: _totalHarga(b).toDouble(),
        ),
      ),
    ).then((_) => _loadBookings());
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: bgTop,
      appBar: AppBar(
        title: const Text(
          'Booking Saya',
          style: TextStyle(color: navyDark, fontWeight: FontWeight.bold, fontSize: 17),
        ),
        backgroundColor: bgTop,
        elevation: 0,
        surfaceTintColor: Colors.transparent,
        iconTheme: const IconThemeData(color: navyDark),
        centerTitle: false,
      ),
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [bgTop, bgBottom],
          ),
        ),
        child: SafeArea(
          top: false,
          child: loading
              ? const Center(child: CircularProgressIndicator(color: orange))
              : error != null
                  ? _buildError()
                  : bookings.isEmpty
                      ? _buildEmpty()
                      : RefreshIndicator(
                          color: orange,
                          onRefresh: _loadBookings,
                          child: ListView.builder(
                            padding: const EdgeInsets.fromLTRB(20, 12, 20, 24),
                            itemCount: bookings.length,
                            itemBuilder: (context, index) => _bookingCard(bookings[index]),
                          ),
                        ),
        ),
      ),
    );
  }

  Widget _buildError() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.error_outline_rounded, color: Colors.grey.shade400, size: 40),
            const SizedBox(height: 12),
            Text(error!, textAlign: TextAlign.center, style: TextStyle(color: Colors.grey.shade600)),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: _loadBookings,
              style: ElevatedButton.styleFrom(backgroundColor: orange, foregroundColor: Colors.white),
              child: const Text('Coba Lagi'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildEmpty() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 64,
              height: 64,
              decoration: BoxDecoration(color: navyLight.withValues(alpha: 0.08), shape: BoxShape.circle),
              child: Icon(Icons.event_busy_rounded, color: navyLight.withValues(alpha: 0.5), size: 28),
            ),
            const SizedBox(height: 12),
            Text('Belum ada booking', style: TextStyle(color: Colors.grey.shade600, fontSize: 13)),
          ],
        ),
      ),
    );
  }

  Widget _bookingCard(dynamic b) {
    final status = _statusBooking(b);
    final total = _totalHarga(b);
    final belumBayar = _belumBayar(b);
    final statusColor = _statusColor(status);

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(color: navyDark.withValues(alpha: 0.06), blurRadius: 12, offset: const Offset(0, 4)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: Text(
                  _namaMobil(b),
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: navyDark),
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: statusColor.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  status.isNotEmpty && status != '-'
                      ? status[0].toUpperCase() + status.substring(1)
                      : '-',
                  style: TextStyle(fontSize: 11, color: statusColor, fontWeight: FontWeight.w600),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Icon(Icons.date_range_rounded, size: 15, color: Colors.grey.shade500),
              const SizedBox(width: 6),
              Text(
                '${b['tanggal_mulai'] ?? '-'} s/d ${b['tanggal_selesai'] ?? '-'}',
                style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                total > 0 ? _rupiah(total) : '-',
                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: orange),
              ),
              if (belumBayar)
                Container(
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(colors: [amber, orange]),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Material(
                    color: Colors.transparent,
                    child: InkWell(
                      borderRadius: BorderRadius.circular(12),
                      onTap: () => _bayarSekarang(b),
                      child: const Padding(
                        padding: EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                        child: Text(
                          'Bayar Sekarang',
                          style: TextStyle(color: Colors.white, fontWeight: FontWeight.w600, fontSize: 12),
                        ),
                      ),
                    ),
                  ),
                )
              else
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  decoration: BoxDecoration(
                    color: teal.withValues(alpha: 0.12),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.check_circle_rounded, size: 14, color: teal),
                      SizedBox(width: 4),
                      Text('Lunas', style: TextStyle(fontSize: 11, color: teal, fontWeight: FontWeight.w600)),
                    ],
                  ),
                ),
            ],
          ),
        ],
      ),
    );
  }
}