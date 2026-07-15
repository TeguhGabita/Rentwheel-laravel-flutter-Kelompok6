import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../booking/booking_saya_screen.dart';
import '../booking/booking_screen.dart';
import '../mobil/cari_mobil_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  // ==================== PALET WARNA ====================
  static const Color navyDark = Color(0xFF14213D);
  static const Color navyLight = Color(0xFF223159);
  static const Color amber = Color(0xFFFFB703);
  static const Color orange = Color(0xFFFB8500);
  static const Color teal = Color(0xFF2EC4B6);
  static const Color bgTop = Color(0xFFFFF6EA);
  static const Color bgBottom = Color(0xFFE9F3F4);

  static const List<Color> brandPalette = [
    Color(0xFFFB8500), // orange
    Color(0xFF2EC4B6), // teal
    Color(0xFFEF476F), // pink/red
    Color(0xFF7B61FF), // purple
    Color(0xFF06A77D), // green
    Color(0xFFFFB703), // amber
  ];

  // ==================== USER & NOTIFIKASI ====================
  String userName = '';
  List notifikasiList = [];
  int unreadCount = 0;

  // ==================== STATS RINGKASAN ====================
  int bookingAktifCount = 0;
  int riwayatBookingCount = 0;
  int menungguPembayaranCount = 0;
  bool loadingStats = true;

  // Booking aktif ('dipesan'/'berjalan') paling baru, untuk ditampilkan
  // sebagai shortcut card di Beranda. Null kalau tidak ada booking aktif.
  Map<String, dynamic>? bookingTerakhir;

  // ==================== MOBIL REKOMENDASI ====================
  List mobilRekomendasi = [];
  bool loadingRekomendasi = true;

  @override
  void initState() {
    super.initState();
    loadUser();
    loadNotifikasi();
    loadStats();
    loadRekomendasi();
  }

  Future<void> loadRekomendasi() async {
    setState(() => loadingRekomendasi = true);
    try {
      final result = await ApiService.getMobil();
      if (result['success'] == true) {
        final List data = result['data'] ?? [];
        final tersedia = data
            .where((item) => (item['status'] ?? '').toString().toLowerCase() == 'tersedia')
            .toList();
        tersedia.sort((a, b) {
          final hargaA = num.tryParse((a['harga_sewa_per_hari'] ?? '0').toString()) ?? 0;
          final hargaB = num.tryParse((b['harga_sewa_per_hari'] ?? '0').toString()) ?? 0;
          return hargaA.compareTo(hargaB);
        });
        if (mounted) {
          setState(() {
            mobilRekomendasi = tersedia.take(6).toList();
            loadingRekomendasi = false;
          });
        }
        return;
      }
    } catch (_) {
      // Gagal ambil data - biarkan kosong, jangan crash halaman Beranda.
    }
    if (mounted) setState(() => loadingRekomendasi = false);
  }

  num _hargaSewa(dynamic item) {
    final raw = item['harga_sewa_per_hari'];
    if (raw == null) return 0;
    return num.tryParse(raw.toString()) ?? 0;
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

  Color _brandColor(String? merk) {
    if (merk == null || merk.isEmpty) return navyLight;
    final hash = merk.codeUnits.fold<int>(0, (prev, c) => prev + c);
    return brandPalette[hash % brandPalette.length];
  }

  Future<void> loadUser() async {
    final user = await ApiService.getSavedUser();
    if (user != null && mounted) {
      setState(() {
        userName = user['name']?.toString() ?? '';
      });
    }
  }

  Future<void> loadNotifikasi() async {
    final result = await ApiService.getNotifikasi();
    if (result['success'] == true && mounted) {
      setState(() {
        notifikasiList = result['data'] ?? [];
        unreadCount = result['unread_count'] ?? 0;
      });
    }
  }

  Future<void> loadStats() async {
    setState(() => loadingStats = true);
    try {
      final result = await ApiService.getBookings();
      if (result['success'] == true) {
        final raw = result['data'];
        final List data = raw is List
            ? raw
            : (raw is Map && raw['data'] is List ? raw['data'] : []);

        int aktif = 0;
        int riwayat = 0;
        int menunggu = 0;
        Map<String, dynamic>? terakhir;
        DateTime? terakhirTanggal;

        for (final b in data) {
          final status = (b['status'] ?? '').toString().toLowerCase();
          if (status == 'dipesan' || status == 'berjalan') {
            aktif++;

            // Cari booking aktif dengan tanggal_mulai paling baru, untuk
            // ditampilkan sebagai shortcut card di Beranda.
            DateTime? tanggalMulai;
            try {
              tanggalMulai = DateTime.parse((b['tanggal_mulai'] ?? '').toString());
            } catch (_) {
              tanggalMulai = null;
            }
            if (terakhir == null ||
                (tanggalMulai != null &&
                    (terakhirTanggal == null || tanggalMulai.isAfter(terakhirTanggal)))) {
              terakhir = Map<String, dynamic>.from(b);
              terakhirTanggal = tanggalMulai;
            }
          } else if (status == 'selesai' || status == 'batal') {
            riwayat++;
          }

          // Pembayaran datang sebagai object nested, bukan field flat:
          // { ..., "pembayaran": { "status_bayar": "pending", ... } }
          // Kalau belum ada record pembayaran sama sekali, itu juga dianggap
          // "menunggu bayar" (booking belum pernah diisi datanya).
          final pembayaran = b['pembayaran'];
          final statusBayar = pembayaran == null
              ? null
              : (pembayaran['status_bayar'] ?? '').toString().toLowerCase();

          final belumAda = pembayaran == null;
          final masihPending = statusBayar == 'pending';
          if ((belumAda || masihPending) && status != 'batal' && status != 'selesai') {
            menunggu++;
          }
        }
        if (mounted) {
          setState(() {
            bookingAktifCount = aktif;
            riwayatBookingCount = riwayat;
            menungguPembayaranCount = menunggu;
            bookingTerakhir = terakhir;
            loadingStats = false;
          });
        }
        return;
      }
    } catch (_) {
      // Gagal ambil data - biarkan default 0, jangan crash halaman Beranda.
    }
    if (mounted) setState(() => loadingStats = false);
  }

  Future<void> _bukaNotifikasi(dynamic notif) async {
    if (notif['read_at'] == null) {
      await ApiService.bacaNotifikasi(notif['id'].toString());
      loadNotifikasi();
    }
  }

  IconData _notifIcon(dynamic data) {
    final type = (data['type'] ?? data['category'] ?? '').toString().toLowerCase();
    final message = (data['message'] ?? '').toString().toLowerCase();
    final text = '$type $message';

    if (text.contains('booking') || text.contains('pesan')) {
      return Icons.event_available_rounded;
    } else if (text.contains('bayar') || text.contains('payment') || text.contains('pembayaran')) {
      return Icons.payments_rounded;
    } else if (text.contains('batal') || text.contains('cancel')) {
      return Icons.cancel_rounded;
    } else if (text.contains('promo') || text.contains('diskon')) {
      return Icons.local_offer_rounded;
    } else if (text.contains('selesai') || text.contains('sukses') || text.contains('berhasil')) {
      return Icons.check_circle_rounded;
    }
    return Icons.notifications_rounded;
  }

  Color _notifColor(dynamic data) {
    final type = (data['type'] ?? data['category'] ?? '').toString().toLowerCase();
    final message = (data['message'] ?? '').toString().toLowerCase();
    final text = '$type $message';

    if (text.contains('booking') || text.contains('pesan')) return teal;
    if (text.contains('bayar') || text.contains('payment') || text.contains('pembayaran')) return orange;
    if (text.contains('batal') || text.contains('cancel')) return const Color(0xFFEF476F);
    if (text.contains('promo') || text.contains('diskon')) return amber;
    if (text.contains('selesai') || text.contains('sukses') || text.contains('berhasil')) {
      return const Color(0xFF06A77D);
    }
    return navyLight;
  }

  String _timeAgo(dynamic rawDate) {
    if (rawDate == null) return '';
    DateTime? date;
    try {
      date = DateTime.parse(rawDate.toString()).toLocal();
    } catch (_) {
      return '';
    }
    final diff = DateTime.now().difference(date);

    if (diff.inSeconds < 60) return 'Baru saja';
    if (diff.inMinutes < 60) return '${diff.inMinutes} menit lalu';
    if (diff.inHours < 24) return '${diff.inHours} jam lalu';
    if (diff.inDays < 7) return '${diff.inDays} hari lalu';
    if (diff.inDays < 30) return '${(diff.inDays / 7).floor()} minggu lalu';
    return '${date.day}/${date.month}/${date.year}';
  }

  void _showNotifikasiSheet() {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setSheetState) {
            return Container(
              decoration: const BoxDecoration(
                color: Color(0xFFF7F8FA),
                borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
              ),
              constraints: BoxConstraints(maxHeight: MediaQuery.of(context).size.height * 0.75),
              child: SafeArea(
                top: false,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const SizedBox(height: 10),
                    Container(
                      width: 40,
                      height: 4,
                      decoration: BoxDecoration(
                        color: Colors.grey.shade300,
                        borderRadius: BorderRadius.circular(10),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.fromLTRB(20, 16, 12, 12),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Row(
                            children: [
                              const Text('Notifikasi',
                                  style: TextStyle(
                                      fontWeight: FontWeight.bold, fontSize: 18, color: navyDark)),
                              if (unreadCount > 0) ...[
                                const SizedBox(width: 8),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFFEF476F).withValues(alpha: 0.12),
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                  child: Text(
                                    '$unreadCount baru',
                                    style: const TextStyle(
                                        color: Color(0xFFEF476F),
                                        fontSize: 11,
                                        fontWeight: FontWeight.w700),
                                  ),
                                ),
                              ],
                            ],
                          ),
                          if (unreadCount > 0)
                            TextButton.icon(
                              onPressed: () async {
                                await ApiService.bacaSemuaNotifikasi();
                                await loadNotifikasi();
                                setSheetState(() {});
                              },
                              icon: const Icon(Icons.done_all_rounded, size: 16, color: teal),
                              label: const Text('Tandai semua',
                                  style: TextStyle(
                                      fontSize: 12, color: teal, fontWeight: FontWeight.w600)),
                              style: TextButton.styleFrom(
                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                minimumSize: Size.zero,
                                tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                              ),
                            ),
                        ],
                      ),
                    ),
                    const Divider(height: 1),
                    Flexible(
                      child: notifikasiList.isEmpty
                          ? Padding(
                              padding: const EdgeInsets.symmetric(vertical: 60),
                              child: Column(
                                children: [
                                  Container(
                                    width: 64,
                                    height: 64,
                                    decoration: BoxDecoration(
                                      color: navyLight.withValues(alpha: 0.08),
                                      shape: BoxShape.circle,
                                    ),
                                    child: Icon(Icons.notifications_off_outlined,
                                        color: navyLight.withValues(alpha: 0.5), size: 28),
                                  ),
                                  const SizedBox(height: 12),
                                  Text('Belum ada notifikasi',
                                      style: TextStyle(color: Colors.grey.shade500, fontSize: 13)),
                                ],
                              ),
                            )
                          : ListView.builder(
                              padding: const EdgeInsets.fromLTRB(14, 10, 14, 20),
                              shrinkWrap: true,
                              itemCount: notifikasiList.length,
                              itemBuilder: (context, index) {
                                final notif = notifikasiList[index];
                                final data = notif['data'] ?? {};
                                final isUnread = notif['read_at'] == null;
                                final color = _notifColor(data);

                                return Container(
                                  margin: const EdgeInsets.only(bottom: 10),
                                  decoration: BoxDecoration(
                                    color: isUnread ? color.withValues(alpha: 0.06) : Colors.white,
                                    borderRadius: BorderRadius.circular(16),
                                    border: Border.all(
                                      color: isUnread
                                          ? color.withValues(alpha: 0.25)
                                          : Colors.grey.shade200,
                                    ),
                                  ),
                                  child: Material(
                                    color: Colors.transparent,
                                    child: InkWell(
                                      borderRadius: BorderRadius.circular(16),
                                      onTap: () async {
                                        await _bukaNotifikasi(notif);
                                        setSheetState(() {});
                                      },
                                      child: Padding(
                                        padding: const EdgeInsets.all(12),
                                        child: Row(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Container(
                                              width: 42,
                                              height: 42,
                                              decoration: BoxDecoration(
                                                color: color.withValues(alpha: 0.14),
                                                borderRadius: BorderRadius.circular(12),
                                              ),
                                              child: Icon(_notifIcon(data), color: color, size: 20),
                                            ),
                                            const SizedBox(width: 12),
                                            Expanded(
                                              child: Column(
                                                crossAxisAlignment: CrossAxisAlignment.start,
                                                children: [
                                                  Text(
                                                    data['title']?.toString() ??
                                                        data['message']?.toString() ??
                                                        'Notifikasi',
                                                    maxLines: 2,
                                                    overflow: TextOverflow.ellipsis,
                                                    style: TextStyle(
                                                      fontSize: 13,
                                                      height: 1.3,
                                                      fontWeight: isUnread
                                                          ? FontWeight.w700
                                                          : FontWeight.w500,
                                                      color: navyDark,
                                                    ),
                                                  ),
                                                  const SizedBox(height: 4),
                                                  Text(
                                                    _timeAgo(notif['created_at']),
                                                    style: TextStyle(
                                                        fontSize: 11, color: Colors.grey.shade500),
                                                  ),
                                                ],
                                              ),
                                            ),
                                            if (isUnread)
                                              Container(
                                                width: 8,
                                                height: 8,
                                                margin: const EdgeInsets.only(top: 4, left: 6),
                                                decoration: BoxDecoration(
                                                    color: color, shape: BoxShape.circle),
                                              ),
                                          ],
                                        ),
                                      ),
                                    ),
                                  ),
                                );
                              },
                            ),
                    ),
                  ],
                ),
              ),
            );
          },
        );
      },
    );
  }

  String get _initial {
    final trimmed = userName.trim();
    if (trimmed.isEmpty) return 'R';
    final parts = trimmed.split(RegExp(r'\s+'));
    if (parts.length == 1) return parts[0].substring(0, 1).toUpperCase();
    return (parts[0].substring(0, 1) + parts[1].substring(0, 1)).toUpperCase();
  }

  void _bukaCariMobil() {
    Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => const CariMobilScreen()),
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
          child: RefreshIndicator(
            color: orange,
            onRefresh: () async {
              await loadNotifikasi();
              await loadStats();
              await loadRekomendasi();
            },
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                _hero(),
                const SizedBox(height: 16),
                _pembayaranBanner(),
                _bookingTerakhirCard(),
                const SizedBox(height: 2),
                _buildMenuGrid(),
                const SizedBox(height: 16),
                _promoCard(),
                const SizedBox(height: 22),
                _rekomendasiSection(),
                const SizedBox(height: 24),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // ==================== HERO (identitas + sapaan + pintasan cari + stats) ====================
  Widget _hero() {
    return Container(
      padding: const EdgeInsets.fromLTRB(20, 16, 20, 22),
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [navyDark, navyLight],
        ),
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(28),
          bottomRight: Radius.circular(28),
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Baris atas: logo app + notifikasi + logout
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Container(
                    width: 32,
                    height: 32,
                    decoration: BoxDecoration(
                      color: amber,
                      borderRadius: BorderRadius.circular(9),
                    ),
                    child: const Icon(Icons.directions_car_filled_rounded, color: navyDark, size: 17),
                  ),
                  const SizedBox(width: 9),
                  const Text(
                    'RentWheel',
                    style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                ],
              ),
              Row(
                children: [
                  Stack(
                    clipBehavior: Clip.none,
                    children: [
                      IconButton(
                        onPressed: _showNotifikasiSheet,
                        icon: const Icon(Icons.notifications_outlined, color: Colors.white70, size: 21),
                      ),
                      if (unreadCount > 0)
                        Positioned(
                          right: 6,
                          top: 6,
                          child: Container(
                            padding: const EdgeInsets.all(3),
                            decoration: const BoxDecoration(
                              color: Color(0xFFEF476F),
                              shape: BoxShape.circle,
                            ),
                            constraints: const BoxConstraints(minWidth: 16, minHeight: 16),
                            child: Text(
                              unreadCount > 9 ? '9+' : '$unreadCount',
                              textAlign: TextAlign.center,
                              style: const TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold),
                            ),
                          ),
                        ),
                    ],
                  ),
                  IconButton(
                    onPressed: () async {
                      await ApiService.logout();
                      if (!mounted) return;
                      Navigator.pushReplacementNamed(context, "/login");
                    },
                    icon: const Icon(Icons.logout, color: Colors.white70, size: 19),
                  ),
                ],
              ),
            ],
          ),
          const SizedBox(height: 14),

          // Sapaan + avatar
          Row(
            children: [
              Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  color: amber,
                  borderRadius: BorderRadius.circular(13),
                ),
                alignment: Alignment.center,
                child: Text(
                  _initial,
                  style: const TextStyle(color: navyDark, fontSize: 15, fontWeight: FontWeight.bold),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Selamat datang,',
                        style: TextStyle(color: Color(0xFF9AA6B8), fontSize: 11, fontWeight: FontWeight.w500)),
                    const SizedBox(height: 1),
                    Text(
                      userName.isNotEmpty ? userName : 'User RentWheel',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 18),

          // Search bar sekarang jadi pintasan ke halaman Cari Mobil.
          Material(
            color: Colors.white,
            borderRadius: BorderRadius.circular(14),
            child: InkWell(
              borderRadius: BorderRadius.circular(14),
              onTap: _bukaCariMobil,
              child: Container(
                padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 16),
                child: Row(
                  children: [
                    Icon(Icons.search, color: Colors.grey.shade500),
                    const SizedBox(width: 10),
                    Text(
                      'Cari mobil (nama atau merk)...',
                      style: TextStyle(color: Colors.grey.shade500, fontSize: 13),
                    ),
                  ],
                ),
              ),
            ),
          ),
          const SizedBox(height: 18),

          // Stats menyatu di dalam hero (kartu kaca transparan)
          Row(
            children: [
              _heroStat(Icons.bolt_rounded, 'Booking aktif', bookingAktifCount),
              const SizedBox(width: 10),
              _heroStat(Icons.access_time_rounded, 'Riwayat', riwayatBookingCount),
              const SizedBox(width: 10),
              _heroStat(Icons.credit_card_rounded, 'Nunggu bayar', menungguPembayaranCount),
            ],
          ),
        ],
      ),
    );
  }

  Widget _heroStat(IconData icon, String label, int value) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 12),
        decoration: BoxDecoration(
          color: Colors.white.withValues(alpha: 0.08),
          borderRadius: BorderRadius.circular(15),
          border: Border.all(color: Colors.white.withValues(alpha: 0.12)),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: amber, size: 16),
            const SizedBox(height: 6),
            loadingStats
                ? const SizedBox(
                    width: 12,
                    height: 12,
                    child: CircularProgressIndicator(strokeWidth: 2, color: amber),
                  )
                : Text(
                    '$value',
                    style: const TextStyle(color: Colors.white, fontSize: 17, fontWeight: FontWeight.bold),
                  ),
            const SizedBox(height: 2),
            Text(
              label,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(color: Color(0xFFAEB8C6), fontSize: 10),
            ),
          ],
        ),
      ),
    );
  }

  // ==================== BANNER SELESAIKAN PEMBAYARAN ====================
  Widget _pembayaranBanner() {
    if (loadingStats || menungguPembayaranCount <= 0) {
      return const SizedBox.shrink();
    }

    final jamak = menungguPembayaranCount > 1;
    final judul = jamak
        ? 'Kamu punya $menungguPembayaranCount booking menunggu pembayaran'
        : 'Kamu punya 1 booking menunggu pembayaran';
    final subJudul = jamak
        ? 'Selesaikan sekarang biar unit tidak dibatalkan otomatis.'
        : 'Selesaikan sekarang biar unitnya tidak dibatalkan otomatis.';

    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 0, 16, 12),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(18),
          onTap: () {
            Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const BookingSayaScreen()),
            ).then((_) => loadStats());
          },
          child: Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: const Color(0xFFEF476F).withValues(alpha: 0.10),
              borderRadius: BorderRadius.circular(18),
              border: Border.all(color: const Color(0xFFEF476F).withValues(alpha: 0.35)),
            ),
            child: Row(
              children: [
                Container(
                  width: 42,
                  height: 42,
                  decoration: BoxDecoration(
                    color: const Color(0xFFEF476F).withValues(alpha: 0.16),
                    borderRadius: BorderRadius.circular(13),
                  ),
                  child: const Icon(Icons.credit_card_off_rounded, color: Color(0xFFEF476F), size: 21),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        judul,
                        style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 13,
                          color: navyDark,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        subJudul,
                        style: TextStyle(fontSize: 11, color: Colors.grey.shade700),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 6),
                const Icon(Icons.arrow_forward_ios_rounded, color: Color(0xFFEF476F), size: 14),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // ==================== CARD BOOKING AKTIF TERAKHIR ====================
  // Shortcut supaya user tidak perlu masuk ke "Booking Saya" hanya untuk
  // melihat status booking yang lagi jalan. Muncul kalau ada booking dengan
  // status 'dipesan' atau 'berjalan' (dipilih yang tanggal_mulai paling baru).
  Widget _bookingTerakhirCard() {
    if (loadingStats || bookingTerakhir == null) {
      return const SizedBox.shrink();
    }

    final b = bookingTerakhir!;
    final namaMobil = (b['mobil'] is Map
            ? (b['mobil']['nama_mobil'] ?? b['mobil']['nama'])
            : b['nama_mobil']) ??
        '-';
    final status = (b['status'] ?? '-').toString();
    final isBerjalan = status.toLowerCase() == 'berjalan';
    final tanggalMulai = b['tanggal_mulai'] ?? '-';
    final tanggalSelesai = b['tanggal_selesai'] ?? '-';

    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 0, 16, 12),
      child: Material(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        child: InkWell(
          borderRadius: BorderRadius.circular(18),
          onTap: () {
            Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const BookingSayaScreen()),
            ).then((_) => loadStats());
          },
          child: Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(18),
              border: Border.all(color: Colors.grey.shade200),
              boxShadow: [
                BoxShadow(
                  color: navyDark.withValues(alpha: 0.05),
                  blurRadius: 8,
                  offset: const Offset(0, 3),
                ),
              ],
            ),
            child: Row(
              children: [
                Container(
                  width: 42,
                  height: 42,
                  decoration: BoxDecoration(
                    color: (isBerjalan ? teal : amber).withValues(alpha: 0.14),
                    borderRadius: BorderRadius.circular(13),
                  ),
                  child: Icon(
                    isBerjalan ? Icons.directions_car_filled_rounded : Icons.event_available_rounded,
                    color: isBerjalan ? teal : amber,
                    size: 21,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Text(
                            isBerjalan ? 'Sewa sedang berjalan' : 'Booking menunggu jadwal',
                            style: const TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 13,
                              color: navyDark,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 2),
                      Text(
                        '$namaMobil • $tanggalMulai s/d $tanggalSelesai',
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: TextStyle(fontSize: 11, color: Colors.grey.shade600),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 6),
                Icon(Icons.arrow_forward_ios_rounded, color: Colors.grey.shade400, size: 14),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // ==================== MENU GRID (Cari Mobil / Booking Saya / Pembayaran) ====================
  Widget _buildMenuGrid() {
    final menu = [
      {
        'icon': Icons.directions_car_filled_rounded,
        'color': orange,
        'label': 'Cari Mobil',
        'onTap': _bukaCariMobil,
      },
      {
        'icon': Icons.calendar_month_rounded,
        'color': navyLight,
        'label': 'Booking Saya',
        'onTap': () {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const BookingSayaScreen()),
          ).then((_) => loadStats());
        },
      },
      {
        'icon': Icons.payments_rounded,
        'color': const Color(0xFF06A77D),
        'label': 'Pembayaran',
        'onTap': () {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const BookingSayaScreen()),
          ).then((_) => loadStats());
        },
      },
    ];

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Row(
        children: menu.map((m) {
          final color = m['color'] as Color;
          return Expanded(
            child: Padding(
              padding: EdgeInsets.only(right: m == menu.last ? 0 : 12),
              child: Material(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                child: InkWell(
                  borderRadius: BorderRadius.circular(16),
                  onTap: m['onTap'] as void Function()?,
                  child: Container(
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(16),
                      boxShadow: [
                        BoxShadow(
                          color: navyDark.withValues(alpha: 0.05),
                          blurRadius: 8,
                          offset: const Offset(0, 3),
                        ),
                      ],
                    ),
                    child: Column(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            color: color,
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Icon(m['icon'] as IconData, color: Colors.white, size: 20),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          m['label'] as String,
                          textAlign: TextAlign.center,
                          style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: navyDark),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          );
        }).toList(),
      ),
    );
  }

  // ==================== PROMO CARD ====================
  Widget _promoCard() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Container(
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          gradient: const LinearGradient(colors: [amber, orange]),
          borderRadius: BorderRadius.circular(20),
        ),
        child: Row(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: const [
                  Text(
                    'Diskon 20% sewa akhir pekan',
                    style: TextStyle(color: navyDark, fontSize: 15, fontWeight: FontWeight.bold),
                  ),
                  SizedBox(height: 4),
                  Text(
                    'Berlaku untuk semua unit MPV & SUV, Jumat–Minggu.',
                    style: TextStyle(color: Color(0xFF3A2400), fontSize: 11.5),
                  ),
                ],
              ),
            ),
            const Icon(Icons.local_offer_rounded, color: navyDark, size: 30),
          ],
        ),
      ),
    );
  }

  // ==================== MOBIL REKOMENDASI ====================
  Widget _rekomendasiSection() {
    // FIX (empty state): sebelumnya section ini hilang total tanpa penjelasan
    // kalau mobilRekomendasi kosong. Sekarang tetap tampilkan judul + pesan
    // supaya user tahu memang belum ada mobil tersedia, bukan error/loading macet.
    if (!loadingRekomendasi && mobilRekomendasi.isEmpty) {
      return Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Rekomendasi Untukmu',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: navyDark),
            ),
            const SizedBox(height: 12),
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 28),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Colors.grey.shade200),
              ),
              child: Column(
                children: [
                  Icon(Icons.directions_car_outlined, color: Colors.grey.shade400, size: 32),
                  const SizedBox(height: 8),
                  Text(
                    'Belum ada mobil tersedia saat ini',
                    style: TextStyle(color: Colors.grey.shade600, fontSize: 13),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    'Coba cek kembali beberapa saat lagi.',
                    style: TextStyle(color: Colors.grey.shade400, fontSize: 11),
                  ),
                ],
              ),
            ),
          ],
        ),
      );
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Rekomendasi Untukmu',
                style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: navyDark),
              ),
              TextButton(
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const CariMobilScreen()),
                  );
                },
                style: TextButton.styleFrom(
                  padding: EdgeInsets.zero,
                  minimumSize: Size.zero,
                  tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                ),
                child: const Text(
                  'Lihat Semua',
                  style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: orange),
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 12),
        SizedBox(
          height: 190,
          child: loadingRekomendasi
              ? const Center(child: CircularProgressIndicator(color: orange))
              : ListView.builder(
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  itemCount: mobilRekomendasi.length,
                  itemBuilder: (context, index) => _rekomendasiCard(mobilRekomendasi[index]),
                ),
        ),
      ],
    );
  }

  Widget _rekomendasiCard(dynamic item) {
    final harga = _hargaSewa(item);
    final merk = item['merk']?.toString();
    final brandColor = _brandColor(merk);

    return Container(
      width: 148,
      margin: const EdgeInsets.only(right: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: navyDark.withValues(alpha: 0.06),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(18),
          onTap: () {
            Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => BookingScreen(mobilId: item["id"])),
            );
          },
          child: Padding(
            padding: const EdgeInsets.all(12),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: double.infinity,
                  height: 64,
                  decoration: BoxDecoration(
                    color: brandColor.withValues(alpha: 0.12),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  alignment: Alignment.center,
                  child: Icon(Icons.directions_car_rounded, color: brandColor, size: 30),
                ),
                const SizedBox(height: 10),
                Text(
                  item['nama_mobil'] ?? '-',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: navyDark),
                ),
                const SizedBox(height: 2),
                Text(
                  merk ?? '-',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(fontSize: 11, color: Colors.grey.shade600),
                ),
                const SizedBox(height: 6),
                Text(
                  harga > 0 ? '${_rupiah(harga)}/hari' : 'Harga belum tersedia',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 12,
                    color: harga > 0 ? orange : Colors.grey,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}