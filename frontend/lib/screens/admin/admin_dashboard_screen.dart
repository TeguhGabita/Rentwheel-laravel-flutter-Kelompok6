import 'package:flutter/material.dart';
import '../../services/api_service.dart';

// TODO: Sesuaikan path import di bawah ini dengan lokasi file screen kamu.
import 'data_mobil_screen.dart';
import 'laporan_screen.dart';
import 'manajemen_user_screen.dart';
import 'kategori_screen.dart';

class AdminDashboardScreen extends StatefulWidget {
  final String adminName;
  final String adminEmail;

  const AdminDashboardScreen({
    super.key,
    required this.adminName,
    required this.adminEmail,
  });

  @override
  State<AdminDashboardScreen> createState() => _AdminDashboardScreenState();
}

class _AdminDashboardScreenState extends State<AdminDashboardScreen> {
  static const Color primaryYellow = Color(0xFFFBBF24);
  static const Color darkNavy = Color(0xFF0F172A);

  bool loading = true;
  Map<String, dynamic> stats = {};
  int _unreadNotifCount = 0;

  @override
  void initState() {
    super.initState();
    fetchDashboard();
    _loadUnreadCount();
  }

  Future<void> _loadUnreadCount() async {
    final result = await ApiService.getNotifikasi();
    if (!mounted) return;
    if (result['success'] == true) {
      setState(() {
        _unreadNotifCount = result['unread_count'] is int
            ? result['unread_count']
            : int.tryParse(result['unread_count']?.toString() ?? '0') ?? 0;
      });
    }
  }

  Future<void> fetchDashboard() async {
    final result = await ApiService.getDashboard();

    setState(() {
      if (result['success'] == true && result['data'] != null) {
        stats = Map<String, dynamic>.from(result['data']);
      }
      loading = false;
    });
  }

  int _asInt(dynamic value) => int.tryParse(value?.toString() ?? '0') ?? 0;
  double _asDouble(dynamic value) => double.tryParse(value?.toString() ?? '0') ?? 0;

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

  // ==================== NOTIFIKASI (popup sederhana) ====================

  Future<void> _showNotifikasiDialog(BuildContext context) async {
    await showDialog(
      context: context,
      barrierColor: Colors.black.withValues(alpha: 0.4),
      builder: (dialogContext) {
        return Dialog(
          insetPadding: const EdgeInsets.symmetric(horizontal: 24, vertical: 60),
          backgroundColor: Colors.transparent,
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 420, maxHeight: 560),
            child: _NotifikasiDialogContent(),
          ),
        );
      },
    );
    _loadUnreadCount();
  }

  @override
  Widget build(BuildContext context) {
    final isWide = MediaQuery.of(context).size.width >= 800;

    return Scaffold(
      backgroundColor: const Color(0xFFF4F5F7),
      // Drawer hanya dipakai di layar sempit (mobile). Di layar lebar
      // menu tetap ditampilkan sebagai nav horizontal seperti sebelumnya.
      drawer: isWide ? null : _mobileDrawer(context),
      appBar: isWide
          ? null
          : AppBar(
              backgroundColor: darkNavy,
              elevation: 0,
              titleSpacing: 0,
              // Eksplisit dibuat sendiri (bukan mengandalkan auto-leading Scaffold)
              // supaya dipastikan berfungsi membuka Drawer saat ditekan.
              leading: Builder(
                builder: (context) => IconButton(
                  icon: const Icon(Icons.menu_rounded, color: Colors.white),
                  onPressed: () => Scaffold.of(context).openDrawer(),
                ),
              ),
              title: Row(
                children: [
                  Container(
                    width: 32,
                    height: 32,
                    decoration: BoxDecoration(
                      color: primaryYellow,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: const Icon(Icons.directions_car_filled_rounded,
                        color: Colors.black87, size: 18),
                  ),
                  const SizedBox(width: 8),
                  const Text(
                    'RentWheel',
                    style: TextStyle(
                        color: Colors.white,
                        fontSize: 15,
                        fontWeight: FontWeight.bold),
                  ),
                ],
              ),
              actions: [
                Stack(
                  clipBehavior: Clip.none,
                  children: [
                    IconButton(
                      onPressed: () => _showNotifikasiDialog(context),
                      icon: const Icon(Icons.notifications_outlined,
                          color: Colors.white70),
                    ),
                    if (_unreadNotifCount > 0)
                      Positioned(
                        right: 6,
                        top: 6,
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 5, vertical: 1),
                          decoration: BoxDecoration(
                            color: const Color(0xFFEF4444),
                            borderRadius: BorderRadius.circular(20),
                            border: Border.all(color: darkNavy, width: 1.5),
                          ),
                          constraints:
                              const BoxConstraints(minWidth: 16, minHeight: 16),
                          child: Text(
                            _unreadNotifCount > 9
                                ? '9+'
                                : _unreadNotifCount.toString(),
                            textAlign: TextAlign.center,
                            style: const TextStyle(
                                color: Colors.white,
                                fontSize: 9,
                                fontWeight: FontWeight.bold),
                          ),
                        ),
                      ),
                  ],
                ),
                const SizedBox(width: 4),
              ],
            ),
      body: SafeArea(
        child: Column(
          children: [
            if (isWide) _topNavBar(context),
            Expanded(
              child: loading
                  ? const Center(child: CircularProgressIndicator())
                  : RefreshIndicator(
                      onRefresh: fetchDashboard,
                      child: SingleChildScrollView(
                        physics: const AlwaysScrollableScrollPhysics(),
                        padding: const EdgeInsets.all(20),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Dashboard Admin',
                              style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              'Selamat datang di Dashboard RentWheel',
                              style: TextStyle(fontSize: 13, color: Colors.grey.shade600),
                            ),
                            const SizedBox(height: 20),
                            _statsGrid(),
                            const SizedBox(height: 20),
                            LayoutBuilder(
                              builder: (context, constraints) {
                                final isWideContent = constraints.maxWidth >= 700;
                                final statusCard = _statusBookingCard();
                                final pendapatanCard = _pendapatanCard();

                                if (isWideContent) {
                                  return Row(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Expanded(child: statusCard),
                                      const SizedBox(width: 20),
                                      Expanded(child: pendapatanCard),
                                    ],
                                  );
                                }

                                return Column(
                                  children: [
                                    statusCard,
                                    const SizedBox(height: 16),
                                    pendapatanCard,
                                  ],
                                );
                              },
                            ),
                          ],
                        ),
                      ),
                    ),
            ),
          ],
        ),
      ),
    );
  }

  /// Drawer (menu geser dari kiri) khusus tampilan mobile.
  /// Berisi identitas admin di atas, lalu daftar menu, lalu tombol keluar
  /// di bagian bawah.
  Widget _mobileDrawer(BuildContext context) {
    return Drawer(
      backgroundColor: Colors.white,
      child: SafeArea(
        child: Column(
          children: [
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
              color: darkNavy,
              child: Row(
                children: [
                  CircleAvatar(
                    radius: 22,
                    backgroundColor: primaryYellow,
                    child: Text(
                      widget.adminName.isNotEmpty
                          ? widget.adminName[0].toUpperCase()
                          : 'A',
                      style: const TextStyle(
                        color: Colors.black87,
                        fontWeight: FontWeight.bold,
                        fontSize: 18,
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          widget.adminName.isNotEmpty ? widget.adminName : 'Admin',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 15,
                            fontWeight: FontWeight.bold,
                          ),
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 2),
                        Text(
                          widget.adminEmail,
                          style: TextStyle(color: Colors.grey.shade400, fontSize: 12),
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 8),
            Expanded(
              child: ListView(
                padding: const EdgeInsets.symmetric(horizontal: 8),
                children: [
                  _drawerItem(
                    context,
                    icon: Icons.dashboard_outlined,
                    label: 'Dashboard',
                    active: true,
                    onTap: () => Navigator.pop(context),
                  ),
                  _drawerItem(
                    context,
                    icon: Icons.directions_car_outlined,
                    label: 'Data Mobil',
                    onTap: () {
                      Navigator.pop(context);
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (context) => const DataMobilScreen()),
                      );
                    },
                  ),
                  _drawerItem(
                    context,
                    icon: Icons.bar_chart_outlined,
                    label: 'Laporan',
                    onTap: () {
                      Navigator.pop(context);
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (context) => const LaporanScreen()),
                      );
                    },
                  ),
                  _drawerItem(
                    context,
                    icon: Icons.people_outline,
                    label: 'Manajemen User',
                    onTap: () {
                      Navigator.pop(context);
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (context) => const ManajemenUserScreen()),
                      );
                    },
                  ),
                  _drawerItem(
                    context,
                    icon: Icons.category_outlined,
                    label: 'Kategori',
                    onTap: () {
                      Navigator.pop(context);
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (context) => const KategoriScreen()),
                      );
                    },
                  ),
                ],
              ),
            ),
            const Divider(height: 1),
            Padding(
              padding: const EdgeInsets.all(12),
              child: ListTile(
                leading: const Icon(Icons.logout_rounded, color: Colors.redAccent),
                title: const Text(
                  'Keluar',
                  style: TextStyle(color: Colors.redAccent, fontWeight: FontWeight.w600),
                ),
                onTap: () async {
                  await ApiService.logout();
                  if (!context.mounted) return;
                  Navigator.pushReplacementNamed(context, '/login');
                },
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _drawerItem(
    BuildContext context, {
    required IconData icon,
    required String label,
    bool active = false,
    required VoidCallback onTap,
  }) {
    return ListTile(
      leading: Icon(icon, color: active ? primaryYellow : Colors.grey.shade700),
      title: Text(
        label,
        style: TextStyle(
          color: active ? darkNavy : Colors.grey.shade800,
          fontWeight: active ? FontWeight.bold : FontWeight.normal,
        ),
      ),
      selected: active,
      selectedTileColor: primaryYellow.withValues(alpha: 0.12),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      onTap: onTap,
    );
  }

  /// Nav bar horizontal, hanya ditampilkan di layar lebar (tablet/web).
  Widget _topNavBar(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
      decoration: BoxDecoration(
        color: darkNavy,
        boxShadow: [
          BoxShadow(color: Colors.black.withValues(alpha: 0.15), blurRadius: 8),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(
              color: primaryYellow,
              borderRadius: BorderRadius.circular(8),
            ),
            child: const Icon(Icons.directions_car_filled_rounded, color: Colors.black87, size: 20),
          ),
          const SizedBox(width: 10),
          const Text(
            'RentWheel',
            style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold),
          ),
          const SizedBox(width: 36),
          Expanded(
            child: SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: [
                  _navItem('Dashboard', active: true, onTap: () {
                    // Sudah di halaman Dashboard, tidak perlu navigasi.
                  }),
                  _navItem(
                    'Data Mobil',
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (context) => const DataMobilScreen()),
                      );
                    },
                  ),
                  _navItem(
                    'Laporan',
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (context) => const LaporanScreen()),
                      );
                    },
                  ),
                  _navItem(
                    'Manajemen User',
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (context) => const ManajemenUserScreen()),
                      );
                    },
                  ),
                  _navItem(
                    'Kategori',
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (context) => const KategoriScreen()),
                      );
                    },
                  ),
                ],
              ),
            ),
          ),
          Stack(
            clipBehavior: Clip.none,
            children: [
              IconButton(
                onPressed: () => _showNotifikasiDialog(context),
                icon: const Icon(Icons.notifications_outlined, color: Colors.white70),
              ),
              if (_unreadNotifCount > 0)
                Positioned(
                  right: 4,
                  top: 4,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 1),
                    decoration: BoxDecoration(
                      color: const Color(0xFFEF4444),
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: darkNavy, width: 1.5),
                    ),
                    constraints: const BoxConstraints(minWidth: 17, minHeight: 17),
                    child: Text(
                      _unreadNotifCount > 9 ? '9+' : _unreadNotifCount.toString(),
                      textAlign: TextAlign.center,
                      style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                    ),
                  ),
                ),
            ],
          ),
          CircleAvatar(
            radius: 15,
            backgroundColor: primaryYellow,
            child: Text(
              widget.adminName.isNotEmpty ? widget.adminName[0].toUpperCase() : 'A',
              style: const TextStyle(color: Colors.black87, fontWeight: FontWeight.bold, fontSize: 12),
            ),
          ),
          const SizedBox(width: 8),
          Text(
            widget.adminName.isNotEmpty ? widget.adminName : 'Admin',
            style: const TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w600),
          ),
          const SizedBox(width: 16),
          TextButton(
            onPressed: () async {
              await ApiService.logout();
              if (!context.mounted) return;
              Navigator.pushReplacementNamed(context, '/login');
            },
            child: const Text('Keluar', style: TextStyle(color: primaryYellow, fontSize: 13)),
          ),
        ],
      ),
    );
  }

  Widget _navItem(String label, {bool active = false, VoidCallback? onTap}) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(6),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              label,
              style: TextStyle(
                color: active ? primaryYellow : Colors.white70,
                fontSize: 13,
                fontWeight: active ? FontWeight.w600 : FontWeight.normal,
              ),
            ),
            const SizedBox(height: 4),
            if (active)
              Container(width: 20, height: 2, color: primaryYellow),
          ],
        ),
      ),
    );
  }

  Widget _statsGrid() {
    final items = [
      _StatData('Total Mobil', _asInt(stats['total_mobil']).toString(),
          Icons.directions_car_outlined, const Color(0xFFFDF3D7), primaryYellow),
      _StatData('Total Pelanggan', _asInt(stats['total_user']).toString(),
          Icons.people_outline, const Color(0xFFE0EEFD), Colors.blue),
      _StatData('Total Booking', _asInt(stats['total_booking']).toString(),
          Icons.receipt_long_outlined, const Color(0xFFEFE5FB), Colors.purple),
      _StatData('Total Pembayaran', _asInt(stats['total_pembayaran']).toString(),
          Icons.attach_money_outlined, const Color(0xFFE1F6EC), Colors.green),
    ];

    return LayoutBuilder(
      builder: (context, constraints) {
        // 1 kolom untuk HP sempit, 2 kolom untuk HP lebar/kecil-tablet, 4 untuk desktop.
        final crossAxisCount = constraints.maxWidth >= 900
            ? 4
            : constraints.maxWidth >= 500
                ? 2
                : 2;
        // Aspect ratio diperkecil (kartu jadi lebih tinggi) supaya angka besar
        // tidak overflow di layar HP sempit.
        final aspectRatio = constraints.maxWidth >= 900 ? 1.7 : 1.15;

        return GridView.count(
          crossAxisCount: crossAxisCount,
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          mainAxisSpacing: 14,
          crossAxisSpacing: 14,
          childAspectRatio: aspectRatio,
          children: items.map((item) => _statCard(item)).toList(),
        );
      },
    );
  }

  Widget _statCard(_StatData item) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.grey.shade200),
      ),
      // mainAxisSize.min + Flexible dipakai (bukan Spacer) supaya kartu
      // mengikuti tinggi kontennya sendiri dan tidak overflow saat ruang sempit.
      child: Column(
        mainAxisSize: MainAxisSize.min,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 32,
            height: 32,
            decoration: BoxDecoration(
              color: item.bgColor,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(item.icon, color: item.iconColor, size: 16),
          ),
          const SizedBox(height: 8),
          Text(item.label,
              style: TextStyle(fontSize: 11.5, color: Colors.grey.shade600),
              overflow: TextOverflow.ellipsis),
          const SizedBox(height: 2),
          Text(
            item.value,
            style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ),
    );
  }

  Widget _statusBookingCard() {
    final berjalan = _asInt(stats['booking_berjalan']);
    final selesai = _asInt(stats['booking_selesai']);

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Status Booking', style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
          const SizedBox(height: 16),
          _statusRow('Booking Berjalan', berjalan, primaryYellow),
          const Divider(height: 24),
          _statusRow('Booking Selesai', selesai, Colors.green),
        ],
      ),
    );
  }

  Widget _statusRow(String label, int value, Color dotColor) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Row(
          children: [
            Container(
              width: 8,
              height: 8,
              decoration: BoxDecoration(color: dotColor, shape: BoxShape.circle),
            ),
            const SizedBox(width: 10),
            Text(label, style: const TextStyle(fontSize: 14)),
          ],
        ),
        Text(value.toString(), style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w600)),
      ],
    );
  }

  Widget _pendapatanCard() {
    final pendapatan = _asDouble(stats['pendapatan']);

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: darkNavy,
        borderRadius: BorderRadius.circular(14),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Pendapatan', style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: Colors.white)),
          const SizedBox(height: 16),
          Text(
            _rupiah(pendapatan),
            style: const TextStyle(fontSize: 26, fontWeight: FontWeight.bold, color: Color(0xFF4ADE80)),
          ),
          const SizedBox(height: 4),
          Text(
            'Total pembayaran yang telah lunas.',
            style: TextStyle(fontSize: 12, color: Colors.grey.shade400),
          ),
        ],
      ),
    );
  }
}

class _StatData {
  final String label;
  final String value;
  final IconData icon;
  final Color bgColor;
  final Color iconColor;

  _StatData(this.label, this.value, this.icon, this.bgColor, this.iconColor);
}

/// Isi popup notifikasi. Terpisah jadi StatefulWidget sendiri supaya
/// bisa punya state loading/list sendiri tanpa mengganggu dashboard utama.
///
/// PENTING: backend pakai Laravel Database Notification (`$user->notifications()`),
/// jadi setiap item punya bentuk:
///   { "id": "...", "data": { ...isi notifikasi asli... }, "read_at": null atau timestamp, "created_at": "..." }
/// Bukan field rata (flat) seperti { "title": ..., "message": ..., "is_read": ... }.
/// Makanya semua akses field pesan/judul harus lewat notif['data'][...],
/// dan status baca dicek dari notif['read_at'] (null = belum dibaca).
class _NotifikasiDialogContent extends StatefulWidget {
  @override
  State<_NotifikasiDialogContent> createState() => _NotifikasiDialogContentState();
}

class _NotifikasiDialogContentState extends State<_NotifikasiDialogContent> {
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

  Future<void> _tandaiSudahDibaca(dynamic notif) async {
    // read_at null artinya belum dibaca (struktur Laravel Database Notification)
    if (notif['read_at'] != null) return;

    final result = await ApiService.bacaNotifikasi(notif['id']);
    if (!mounted) return;

    if (result['success'] == true) {
      setState(() {
        notif['read_at'] = DateTime.now().toIso8601String();
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

  /// Warna aksen per jenis notifikasi.
  Color _colorForType(String? type) {
    switch (type) {
      case 'BookingBaruNotification':
        return const Color(0xFF6366F1); // indigo
      case 'PembayaranBaruNotification':
        return const Color(0xFF10B981); // hijau
      case 'BookingDikonfirmasiNotification':
        return const Color(0xFFF59E0B); // amber
      default:
        return Colors.grey;
    }
  }

  IconData _iconForType(String? type) {
    switch (type) {
      case 'BookingBaruNotification':
        return Icons.event_note_rounded;
      case 'PembayaranBaruNotification':
        return Icons.payments_rounded;
      case 'BookingDikonfirmasiNotification':
        return Icons.check_circle_rounded;
      default:
        return Icons.notifications_rounded;
    }
  }

  /// Format waktu relatif sederhana ("baru saja", "5 menit lalu", dst).
  String _waktuRelatif(dynamic createdAt) {
    if (createdAt == null) return '';
    final date = DateTime.tryParse(createdAt.toString());
    if (date == null) return '';

    final diff = DateTime.now().difference(date);
    if (diff.inSeconds < 60) return 'Baru saja';
    if (diff.inMinutes < 60) return '${diff.inMinutes} menit lalu';
    if (diff.inHours < 24) return '${diff.inHours} jam lalu';
    if (diff.inDays < 7) return '${diff.inDays} hari lalu';
    return '${date.day}/${date.month}/${date.year}';
  }

  @override
  Widget build(BuildContext context) {
    final unreadCount = _notifikasi.where((n) => n['read_at'] == null).length;

    return ClipRRect(
      borderRadius: BorderRadius.circular(20),
      child: Material(
        color: Colors.white,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.fromLTRB(20, 18, 12, 18),
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  colors: [Color(0xFF0F172A), Color(0xFF1E293B)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
              ),
              child: Row(
                children: [
                  const Icon(Icons.notifications_rounded, color: Colors.white, size: 22),
                  const SizedBox(width: 10),
                  const Expanded(
                    child: Text(
                      'Notifikasi',
                      style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold, color: Colors.white),
                    ),
                  ),
                  if (unreadCount > 0)
                    Container(
                      margin: const EdgeInsets.only(right: 8),
                      padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 3),
                      decoration: BoxDecoration(
                        color: const Color(0xFFFBBF24),
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: Text(
                        '$unreadCount baru',
                        style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                      ),
                    ),
                  IconButton(
                    icon: const Icon(Icons.close_rounded, size: 20, color: Colors.white70),
                    onPressed: () => Navigator.of(context).pop(),
                    splashRadius: 18,
                  ),
                ],
              ),
            ),
            Flexible(child: _buildBody()),
          ],
        ),
      ),
    );
  }

  Widget _buildBody() {
    if (_loading) {
      return const Padding(
        padding: EdgeInsets.all(40),
        child: Center(child: CircularProgressIndicator()),
      );
    }

    if (_errorMessage != null) {
      return Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.error_outline_rounded, color: Colors.grey.shade400, size: 40),
            const SizedBox(height: 12),
            Text(_errorMessage!, textAlign: TextAlign.center, style: TextStyle(color: Colors.grey.shade700)),
            const SizedBox(height: 12),
            TextButton(onPressed: _loadNotifikasi, child: const Text('Coba Lagi')),
          ],
        ),
      );
    }

    if (_notifikasi.isEmpty) {
      return Padding(
        padding: const EdgeInsets.all(40),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.notifications_off_outlined, color: Colors.grey.shade300, size: 44),
            const SizedBox(height: 12),
            Text('Belum ada notifikasi.', style: TextStyle(color: Colors.grey.shade500, fontSize: 13)),
          ],
        ),
      );
    }

    return ListView.builder(
      shrinkWrap: true,
      padding: const EdgeInsets.symmetric(vertical: 6),
      itemCount: _notifikasi.length,
      itemBuilder: (context, index) {
        final notif = _notifikasi[index];
        final data = _dataOf(notif);
        final bool isRead = notif['read_at'] != null;
        final accentColor = _colorForType(notif['type']?.toString());

        return InkWell(
          onTap: () => _tandaiSudahDibaca(notif),
          child: Container(
            margin: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: isRead ? Colors.white : accentColor.withValues(alpha: 0.06),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(
                color: isRead ? Colors.grey.shade200 : accentColor.withValues(alpha: 0.25),
              ),
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 38,
                  height: 38,
                  decoration: BoxDecoration(
                    color: accentColor.withValues(alpha: isRead ? 0.12 : 0.16),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Icon(_iconForType(notif['type']?.toString()), size: 19, color: accentColor),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Expanded(
                            child: Text(
                              _judulOf(notif),
                              style: TextStyle(
                                fontSize: 13.5,
                                fontWeight: isRead ? FontWeight.w600 : FontWeight.bold,
                                color: const Color(0xFF0F172A),
                              ),
                            ),
                          ),
                          if (!isRead)
                            Container(
                              width: 7,
                              height: 7,
                              margin: const EdgeInsets.only(left: 6, top: 3),
                              decoration: const BoxDecoration(color: Color(0xFFEF4444), shape: BoxShape.circle),
                            ),
                        ],
                      ),
                      const SizedBox(height: 3),
                      Text(
                        _pesanOf(data),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: TextStyle(fontSize: 12, color: Colors.grey.shade600, height: 1.3),
                      ),
                      const SizedBox(height: 5),
                      Text(
                        _waktuRelatif(notif['created_at']),
                        style: TextStyle(fontSize: 10.5, color: Colors.grey.shade400, fontWeight: FontWeight.w500),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}