import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../pembayaran/pembayaran_screen.dart';

class BookingScreen extends StatefulWidget {
  final int mobilId;

  const BookingScreen({
    super.key,
    required this.mobilId,
  });

  @override
  State<BookingScreen> createState() => _BookingScreenState();
}

class _BookingScreenState extends State<BookingScreen> {
  // ==================== PALET WARNA (sama dengan HomeScreen) ====================
  static const Color navyDark = Color(0xFF14213D);
  static const Color navyLight = Color(0xFF223159);
  static const Color amber = Color(0xFFFFB703);
  static const Color orange = Color(0xFFFB8500);
  static const Color teal = Color(0xFF2EC4B6);
  static const Color bgTop = Color(0xFFFFF6EA);
  static const Color bgBottom = Color(0xFFE9F3F4);

  final tanggalMulai = TextEditingController();
  final tanggalSelesai = TextEditingController();

  String metodePembayaran = 'tunai';
  String metodeVirtual = 'bank_transfer';

  bool loading = false;

  Future<void> _pickDate(TextEditingController controller) async {
    final now = DateTime.now();
    final picked = await showDatePicker(
      context: context,
      initialDate: now,
      firstDate: now,
      lastDate: DateTime(now.year + 2),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: orange,
              onPrimary: Colors.white,
              onSurface: navyDark,
            ),
          ),
          child: child!,
        );
      },
    );
    if (picked != null) {
      final formatted =
          "${picked.year}-${picked.month.toString().padLeft(2, '0')}-${picked.day.toString().padLeft(2, '0')}";
      setState(() => controller.text = formatted);
    }
  }

  Future<void> booking() async {
    setState(() {
      loading = true;
    });

    final payload = {
      "mobil_id": widget.mobilId,
      "tanggal_mulai": tanggalMulai.text,
      "tanggal_selesai": tanggalSelesai.text,
      "metode_pembayaran": metodePembayaran,
    };

    if (metodePembayaran == 'virtual') {
      payload["metode_virtual"] = metodeVirtual;
    }

    final result = await ApiService.createBooking(payload);

    setState(() {
      loading = false;
    });

    if (!mounted) return;

    if (result["success"] != true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result["message"] ?? "Booking gagal")),
      );
      return;
    }

    final data = result["data"];
    final bookingData = (data is Map && data["booking"] is Map)
        ? data["booking"] as Map
        : data as Map?;

    final bookingId = bookingData?["id"];
    final rawTotal = bookingData?["total_harga"] ?? bookingData?["total_bayar"];
    final totalHarga = double.tryParse(rawTotal?.toString() ?? '') ?? 0;

    if (bookingId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Booking berhasil, silakan cek riwayat booking untuk pembayaran."),
        ),
      );
      Navigator.pop(context);
      return;
    }

    if (metodePembayaran == 'virtual') {
      final metodeAwal = switch (metodeVirtual) {
        'bank_transfer' => 'Transfer Bank',
        'e_wallet' => 'E-Wallet',
        'qris' => 'QRIS',
        _ => 'Transfer Bank',
      };

      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (_) => PembayaranScreen(
            bookingId: bookingId is int ? bookingId : int.parse(bookingId.toString()),
            totalBayar: totalHarga,
            metodeAwal: metodeAwal,
          ),
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Booking berhasil")),
      );
      Navigator.pop(context);
    }
  }

  @override
  void dispose() {
    tanggalMulai.dispose();
    tanggalSelesai.dispose();
    super.dispose();
  }

  Widget _sectionCard({required Widget child, EdgeInsetsGeometry? padding}) {
    return Container(
      width: double.infinity,
      padding: padding ?? const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: navyDark.withValues(alpha: 0.06),
            blurRadius: 16,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: child,
    );
  }

  Widget _sectionTitle(IconData icon, String text) {
    return Row(
      children: [
        Container(
          width: 30,
          height: 30,
          decoration: BoxDecoration(
            color: teal.withValues(alpha: 0.12),
            borderRadius: BorderRadius.circular(9),
          ),
          child: Icon(icon, size: 16, color: teal),
        ),
        const SizedBox(width: 10),
        Text(
          text,
          style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: navyDark),
        ),
      ],
    );
  }

  // FIX: dibungkus GestureDetector + AbsorbPointer supaya tap konsisten
  // ke-detect di Flutter Web (menghindari bug engine terkait
  // "targeted input element must be the active input element" pada
  // TextField(readOnly:true) + onTap di web).
  Widget _dateField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
  }) {
    return GestureDetector(
      onTap: () => _pickDate(controller),
      behavior: HitTestBehavior.opaque,
      child: AbsorbPointer(
        child: TextField(
          controller: controller,
          readOnly: true,
          style: const TextStyle(fontSize: 14, color: navyDark, fontWeight: FontWeight.w600),
          decoration: InputDecoration(
            labelText: label,
            labelStyle: TextStyle(color: Colors.grey.shade600, fontSize: 13),
            hintText: "Pilih tanggal",
            prefixIcon: Icon(icon, size: 20, color: teal),
            filled: true,
            fillColor: const Color(0xFFF6F6F6),
            contentPadding: const EdgeInsets.symmetric(vertical: 14, horizontal: 16),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: BorderSide.none,
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: BorderSide(color: Colors.grey.shade300),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: orange, width: 1.5),
            ),
          ),
        ),
      ),
    );
  }

  /// Kartu pilihan metode utama (Tunai / Virtual) — pengganti RadioListTile.
  Widget _paymentMainOption({
    required String value,
    required String label,
    required String subtitle,
    required IconData icon,
    required Color color,
  }) {
    final active = metodePembayaran == value;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => metodePembayaran = value),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 180),
          padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 12),
          decoration: BoxDecoration(
            color: active ? color.withValues(alpha: 0.08) : const Color(0xFFF6F6F6),
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: active ? color : Colors.grey.shade300, width: active ? 1.5 : 1),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Container(
                    width: 32,
                    height: 32,
                    decoration: BoxDecoration(
                      color: active ? color : Colors.white,
                      borderRadius: BorderRadius.circular(10),
                      border: active ? null : Border.all(color: Colors.grey.shade300),
                    ),
                    child: Icon(icon, size: 16, color: active ? Colors.white : Colors.grey.shade500),
                  ),
                  if (active)
                    Icon(Icons.check_circle_rounded, size: 18, color: color)
                  else
                    Icon(Icons.circle_outlined, size: 18, color: Colors.grey.shade400),
                ],
              ),
              const SizedBox(height: 10),
              Text(
                label,
                style: TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.bold,
                  color: active ? navyDark : Colors.grey.shade700,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                subtitle,
                style: TextStyle(fontSize: 11, color: Colors.grey.shade500),
              ),
            ],
          ),
        ),
      ),
    );
  }

  /// Chip pilihan sub-metode virtual (Bank Transfer / E-Wallet / QRIS).
  Widget _virtualOptionChip({
    required String value,
    required String label,
    required IconData icon,
  }) {
    final active = metodeVirtual == value;
    return GestureDetector(
      onTap: () => setState(() => metodeVirtual = value),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 180),
        padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 14),
        decoration: BoxDecoration(
          color: active ? orange : Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: active ? orange : Colors.grey.shade300),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 15, color: active ? Colors.white : Colors.grey.shade600),
            const SizedBox(width: 6),
            Text(
              label,
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: active ? Colors.white : navyDark,
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: bgTop,
      appBar: AppBar(
        title: const Text(
          "Booking Mobil",
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
          child: ListView(
            padding: const EdgeInsets.fromLTRB(20, 8, 20, 32),
            children: [
              // Banner info kecil
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(colors: [navyDark, navyLight]),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Row(
                  children: [
                    Container(
                      width: 40,
                      height: 40,
                      decoration: BoxDecoration(
                        color: amber,
                        borderRadius: BorderRadius.circular(11),
                      ),
                      child: const Icon(Icons.event_available_rounded, color: navyDark, size: 20),
                    ),
                    const SizedBox(width: 12),
                    const Expanded(
                      child: Text(
                        "Isi detail sewa dan pilih metode pembayaran di bawah.",
                        style: TextStyle(fontSize: 12.5, color: Colors.white70, height: 1.3),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 18),

              // Kartu: tanggal sewa
              _sectionCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _sectionTitle(Icons.date_range_rounded, "Tanggal Sewa"),
                    const SizedBox(height: 16),
                    _dateField(
                      controller: tanggalMulai,
                      label: "Tanggal Mulai",
                      icon: Icons.calendar_today_rounded,
                    ),
                    const SizedBox(height: 14),
                    _dateField(
                      controller: tanggalSelesai,
                      label: "Tanggal Selesai",
                      icon: Icons.calendar_today_rounded,
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 16),

              // Kartu: metode pembayaran
              _sectionCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _sectionTitle(Icons.payments_rounded, "Metode Pembayaran"),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        _paymentMainOption(
                          value: 'tunai',
                          label: 'Tunai',
                          subtitle: 'Bayar di tempat',
                          icon: Icons.payments_outlined,
                          color: teal,
                        ),
                        const SizedBox(width: 12),
                        _paymentMainOption(
                          value: 'virtual',
                          label: 'Virtual',
                          subtitle: 'Transfer / e-wallet',
                          icon: Icons.account_balance_wallet_outlined,
                          color: orange,
                        ),
                      ],
                    ),
                    AnimatedSize(
                      duration: const Duration(milliseconds: 200),
                      curve: Curves.easeInOut,
                      child: metodePembayaran == 'virtual'
                          ? Padding(
                              padding: const EdgeInsets.only(top: 16),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    "Pilih Metode Virtual",
                                    style: TextStyle(
                                      fontWeight: FontWeight.w600,
                                      fontSize: 12.5,
                                      color: Colors.grey.shade600,
                                    ),
                                  ),
                                  const SizedBox(height: 10),
                                  Wrap(
                                    spacing: 8,
                                    runSpacing: 8,
                                    children: [
                                      _virtualOptionChip(
                                        value: 'bank_transfer',
                                        label: 'Bank Transfer',
                                        icon: Icons.account_balance_rounded,
                                      ),
                                      _virtualOptionChip(
                                        value: 'e_wallet',
                                        label: 'E-Wallet',
                                        icon: Icons.account_balance_wallet_rounded,
                                      ),
                                      _virtualOptionChip(
                                        value: 'qris',
                                        label: 'QRIS',
                                        icon: Icons.qr_code_rounded,
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            )
                          : const SizedBox.shrink(),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 28),

              Container(
                height: 54,
                decoration: BoxDecoration(
                  gradient: const LinearGradient(colors: [amber, orange]),
                  borderRadius: BorderRadius.circular(16),
                  boxShadow: [
                    BoxShadow(
                      color: orange.withValues(alpha: 0.3),
                      blurRadius: 14,
                      offset: const Offset(0, 6),
                    ),
                  ],
                ),
                child: Material(
                  color: Colors.transparent,
                  child: InkWell(
                    borderRadius: BorderRadius.circular(16),
                    onTap: loading ? null : booking,
                    child: Center(
                      child: loading
                          ? const SizedBox(
                              width: 22,
                              height: 22,
                              child: CircularProgressIndicator(strokeWidth: 2.4, color: Colors.white),
                            )
                          : const Text(
                              "Booking Sekarang",
                              style: TextStyle(
                                fontSize: 15,
                                fontWeight: FontWeight.bold,
                                color: Colors.white,
                              ),
                            ),
                    ),
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