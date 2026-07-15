import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../booking/booking_screen.dart';

class CariMobilScreen extends StatefulWidget {
  const CariMobilScreen({super.key});

  @override
  State<CariMobilScreen> createState() => _CariMobilScreenState();
}

enum _SortMode { none, cheapest, expensive }

class _CariMobilScreenState extends State<CariMobilScreen> {
  // ==================== PALET WARNA (sama seperti Beranda) ====================
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

  List mobil = [];
  bool loading = true;

  final TextEditingController searchController = TextEditingController();
  String searchQuery = '';
  String? selectedKategori;
  _SortMode sortMode = _SortMode.none;

  @override
  void initState() {
    super.initState();
    getMobil();
    searchController.addListener(() {
      setState(() => searchQuery = searchController.text.trim().toLowerCase());
    });
  }

  @override
  void dispose() {
    searchController.dispose();
    super.dispose();
  }

  Future<void> getMobil() async {
    setState(() => loading = true);

    final result = await ApiService.getMobil();

    if (result['success'] == true) {
      setState(() {
        mobil = result["data"] ?? [];
        loading = false;
      });
    } else {
      setState(() {
        loading = false;
      });
    }
  }

  String? _kategoriNama(dynamic item) {
    if (item['kategori'] != null && item['kategori'] is Map) {
      return item['kategori']['nama']?.toString();
    }
    if (item['kategori_nama'] != null) {
      return item['kategori_nama'].toString();
    }
    return null;
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

  List get _kategoriOptions {
    final set = <String>{};
    for (final item in mobil) {
      final nama = _kategoriNama(item);
      if (nama != null && nama.isNotEmpty) set.add(nama);
    }
    return set.toList()..sort();
  }

  List get _filteredMobil {
    var list = mobil.where((item) {
      final nama = (item['nama_mobil'] ?? '').toString().toLowerCase();
      final merk = (item['merk'] ?? '').toString().toLowerCase();
      final matchSearch = searchQuery.isEmpty ||
          nama.contains(searchQuery) ||
          merk.contains(searchQuery);

      final matchKategori = selectedKategori == null ||
          _kategoriNama(item) == selectedKategori;

      return matchSearch && matchKategori;
    }).toList();

    if (sortMode == _SortMode.cheapest) {
      list.sort((a, b) => _hargaSewa(a).compareTo(_hargaSewa(b)));
    } else if (sortMode == _SortMode.expensive) {
      list.sort((a, b) => _hargaSewa(b).compareTo(_hargaSewa(a)));
    }

    return list;
  }

  @override
  Widget build(BuildContext context) {
    final kategoriOptions = _kategoriOptions;
    final displayedMobil = _filteredMobil;

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
            onRefresh: getMobil,
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                _header(),
                const SizedBox(height: 16),
                _filterRow(kategoriOptions),
                const SizedBox(height: 10),
                loading
                    ? const Padding(
                        padding: EdgeInsets.symmetric(vertical: 60),
                        child: Center(child: CircularProgressIndicator(color: orange)),
                      )
                    : displayedMobil.isEmpty
                        ? const Padding(
                            padding: EdgeInsets.symmetric(vertical: 60),
                            child: Center(child: Text('Tidak ada mobil yang cocok.')),
                          )
                        : Padding(
                            padding: const EdgeInsets.fromLTRB(16, 0, 16, 24),
                            child: Column(
                              children: displayedMobil.map((item) => _mobilCard(item)).toList(),
                            ),
                          ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // ==================== HEADER (judul halaman + search) ====================
  Widget _header() {
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
          Row(
            children: [
              IconButton(
                onPressed: () => Navigator.pop(context),
                icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 18),
                padding: EdgeInsets.zero,
                constraints: const BoxConstraints(),
              ),
              const SizedBox(width: 8),
              const Text(
                'Cari Mobil',
                style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
              ),
            ],
          ),
          const SizedBox(height: 16),
          TextField(
            controller: searchController,
            autofocus: false,
            style: const TextStyle(fontSize: 14),
            decoration: InputDecoration(
              hintText: 'Cari mobil (nama atau merk)...',
              hintStyle: TextStyle(color: Colors.grey.shade500, fontSize: 13),
              prefixIcon: Icon(Icons.search, color: Colors.grey.shade500),
              suffixIcon: searchQuery.isNotEmpty
                  ? IconButton(
                      icon: Icon(Icons.clear, color: Colors.grey.shade500),
                      onPressed: () => searchController.clear(),
                    )
                  : null,
              filled: true,
              fillColor: Colors.white,
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(14),
                borderSide: BorderSide.none,
              ),
              contentPadding: const EdgeInsets.symmetric(vertical: 0, horizontal: 16),
            ),
          ),
        ],
      ),
    );
  }

  Widget _filterRow(List kategoriOptions) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        child: Row(
          children: [
            _sortChip('Termurah', _SortMode.cheapest),
            const SizedBox(width: 8),
            _sortChip('Termahal', _SortMode.expensive),
            if (kategoriOptions.isNotEmpty) ...[
              const SizedBox(width: 12),
              Container(width: 1, height: 22, color: Colors.grey.shade300),
              const SizedBox(width: 12),
              _kategoriChip('Semua', null),
              ...kategoriOptions.map(
                (k) => Padding(
                  padding: const EdgeInsets.only(left: 8),
                  child: _kategoriChip(k, k),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _sortChip(String label, _SortMode mode) {
    final active = sortMode == mode;
    return ChoiceChip(
      label: Text(label, style: TextStyle(color: active ? Colors.white : navyDark, fontSize: 12)),
      selected: active,
      showCheckmark: false,
      selectedColor: orange,
      backgroundColor: Colors.white,
      side: BorderSide(color: active ? orange : Colors.grey.shade300),
      onSelected: (_) {
        setState(() => sortMode = active ? _SortMode.none : mode);
      },
    );
  }

  Widget _kategoriChip(String label, String? value) {
    final active = selectedKategori == value;
    return ChoiceChip(
      label: Text(label, style: TextStyle(color: active ? Colors.white : navyDark, fontSize: 12)),
      selected: active,
      showCheckmark: false,
      selectedColor: teal,
      backgroundColor: Colors.white,
      side: BorderSide(color: active ? teal : Colors.grey.shade300),
      onSelected: (_) {
        setState(() => selectedKategori = value);
      },
    );
  }

  Widget _mobilCard(dynamic item) {
    final harga = _hargaSewa(item);
    final status = item['status']?.toString() ?? '-';
    final merk = item['merk']?.toString();
    final brandColor = _brandColor(merk);
    final statusColor = status.toLowerCase() == 'tersedia'
        ? const Color(0xFF06A77D)
        : status.toLowerCase() == 'servis'
            ? Colors.orange
            : Colors.grey;

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
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
      child: Row(
        children: [
          Container(
            width: 6,
            height: 96,
            decoration: BoxDecoration(
              color: brandColor,
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(18),
                bottomLeft: Radius.circular(18),
              ),
            ),
          ),
          Expanded(
            child: Padding(
              padding: const EdgeInsets.all(14),
              child: Row(
                children: [
                  Container(
                    width: 52,
                    height: 52,
                    decoration: BoxDecoration(
                      color: brandColor.withValues(alpha: 0.12),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Icon(Icons.directions_car_rounded, color: brandColor, size: 26),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          item["nama_mobil"] ?? "-",
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: navyDark),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          merk ?? "-",
                          style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          harga > 0 ? "${_rupiah(harga)}/hari" : "Harga belum tersedia",
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 13,
                            color: harga > 0 ? orange : Colors.grey,
                            fontStyle: harga > 0 ? FontStyle.normal : FontStyle.italic,
                          ),
                        ),
                        if (status != '-')
                          Padding(
                            padding: const EdgeInsets.only(top: 4),
                            child: Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                              decoration: BoxDecoration(
                                color: statusColor.withValues(alpha: 0.12),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: Text(
                                status[0].toUpperCase() + status.substring(1),
                                style: TextStyle(fontSize: 10, color: statusColor, fontWeight: FontWeight.w600),
                              ),
                            ),
                          ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 8),
                  _bookingButton(item, isAvailable: status.toLowerCase() == 'tersedia'),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _bookingButton(dynamic item, {bool isAvailable = true}) {
    if (!isAvailable) {
      return Container(
        decoration: BoxDecoration(
          color: Colors.grey.shade300,
          borderRadius: BorderRadius.circular(12),
        ),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
        child: Text(
          'Tidak Tersedia',
          style: TextStyle(color: Colors.grey.shade600, fontWeight: FontWeight.w600, fontSize: 11),
        ),
      );
    }

    return Container(
      decoration: BoxDecoration(
        gradient: const LinearGradient(colors: [amber, orange]),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(12),
          onTap: () {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => BookingScreen(mobilId: item["id"]),
              ),
            );
          },
          child: const Padding(
            padding: EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            child: Text(
              'Booking',
              style: TextStyle(color: Colors.white, fontWeight: FontWeight.w600, fontSize: 12),
            ),
          ),
        ),
      ),
    );
  }
}