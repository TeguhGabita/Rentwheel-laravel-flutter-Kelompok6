import 'dart:typed_data'; // Ditambahkan untuk support bytes gambar
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:image_picker/image_picker.dart';
import '../../services/api_service.dart';

class PembayaranScreen extends StatefulWidget {
  final int bookingId;
  final double totalBayar;
  final String? metodeAwal;

  const PembayaranScreen({
    super.key,
    required this.bookingId,
    required this.totalBayar,
    this.metodeAwal,
  });

  @override
  State<PembayaranScreen> createState() => _PembayaranScreenState();
}

class _PembayaranScreenState extends State<PembayaranScreen> {
  // ==================== PALET WARNA (sama dengan HomeScreen & BookingScreen) ====================
  static const Color navyDark = Color(0xFF14213D);
  static const Color navyLight = Color(0xFF223159);
  static const Color amber = Color(0xFFFFB703);
  static const Color orange = Color(0xFFFB8500);
  static const Color teal = Color(0xFF2EC4B6);
  static const Color bgTop = Color(0xFFFFF6EA);
  static const Color bgBottom = Color(0xFFE9F3F4);

  late String metodeBayar;
  bool loading = false;

  // Menggunakan Uint8List & String name agar mendukung Web & Mobile
  Uint8List? _buktiTransferBytes;
  String? _buktiTransferFileName;

  static const _banks = [
    {'nama': 'Bank BCA', 'rekening': '1234567890', 'atasNama': 'PT RentWheel Indonesia'},
    {'nama': 'Bank Mandiri', 'rekening': '0987654321', 'atasNama': 'PT RentWheel Indonesia'},
    {'nama': 'Bank BNI', 'rekening': '1122334455', 'atasNama': 'PT RentWheel Indonesia'},
  ];

  static const _ewallets = [
    {'nama': 'GoPay', 'nomor': '081234567890'},
    {'nama': 'OVO', 'nomor': '081234567890'},
    {'nama': 'DANA', 'nomor': '081234567890'},
  ];

  static const _metodeOptions = [
    {'value': 'Transfer Bank', 'label': 'Transfer Bank', 'icon': Icons.account_balance_rounded},
    {'value': 'E-Wallet', 'label': 'E-Wallet', 'icon': Icons.account_balance_wallet_rounded},
    {'value': 'QRIS', 'label': 'QRIS', 'icon': Icons.qr_code_rounded},
    {'value': 'Cash', 'label': 'Cash', 'icon': Icons.payments_rounded},
  ];

  @override
  void initState() {
    super.initState();
    metodeBayar = widget.metodeAwal ?? "Transfer Bank";
  }

  void _copy(String value, String label) {
    Clipboard.setData(ClipboardData(text: value));
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text('$label disalin')),
    );
  }

  Future<void> _pilihBuktiTransfer() async {
    final picker = ImagePicker();
    final picked = await picker.pickImage(source: ImageSource.gallery, imageQuality: 80);

    if (picked != null) {
      // Ambil bytes gambar agar aman di Flutter Web & Mobile
      final bytes = await picked.readAsBytes();
      setState(() {
        _buktiTransferBytes = bytes;
        _buktiTransferFileName = picked.name;
      });
    }
  }

  bool get _perluBuktiTransfer => metodeBayar == 'Transfer Bank' || metodeBayar == 'E-Wallet';

  Future<void> bayar() async {
    // Validasi: kalau metode butuh bukti transfer, wajib upload dulu.
    if (_perluBuktiTransfer && _buktiTransferBytes == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Silakan upload bukti transfer terlebih dahulu.')),
      );
      return;
    }

    setState(() {
      loading = true;
    });

    // PENGIRIMAN DATA KE API (Mengirim Bytes & Filename agar support Web)
    final result = await ApiService.createPembayaran(
      {
        "booking_id": widget.bookingId,
        "metode_bayar": metodeBayar,
        "jumlah_bayar": widget.totalBayar,
      },
      buktiTransferBytes: _buktiTransferBytes,
      filename: _buktiTransferFileName,
    );
    print("RESULT PEMBAYARAN: $result");
    setState(() {
      loading = false;
    });

    if (!mounted) return;

    final pesan = result["data"]?["message"] ?? result["message"] ?? "Terjadi kesalahan";

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(pesan)),
    );

    if (result["success"] == true) {
      Navigator.pop(context);
    }
  }

  // ==================== WIDGET HELPERS ====================

  Widget _sectionLabel(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Text(
        text,
        style: TextStyle(fontSize: 12.5, fontWeight: FontWeight.w600, color: Colors.grey.shade600),
      ),
    );
  }

  Widget _card({required Widget child, EdgeInsetsGeometry? padding}) {
    return Container(
      width: double.infinity,
      padding: padding ?? const EdgeInsets.all(18),
      margin: const EdgeInsets.only(bottom: 16),
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

  Widget _detailRow({
    required String title,
    required String value,
    String? subtitle,
    required VoidCallback onCopy,
  }) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: const Color(0xFFF6F6F6),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Row(
        children: [
          Container(
            width: 38,
            height: 38,
            decoration: BoxDecoration(
              color: teal.withValues(alpha: 0.14),
              borderRadius: BorderRadius.circular(11),
            ),
            child: const Icon(Icons.account_balance_wallet_rounded, size: 18, color: teal),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: navyDark)),
                const SizedBox(height: 3),
                Text(value,
                    style: const TextStyle(
                        fontSize: 15, letterSpacing: 1, fontWeight: FontWeight.w600, color: navyDark)),
                if (subtitle != null)
                  Padding(
                    padding: const EdgeInsets.only(top: 2),
                    child: Text(subtitle, style: TextStyle(fontSize: 11, color: Colors.grey.shade500)),
                  ),
              ],
            ),
          ),
          Material(
            color: orange.withValues(alpha: 0.12),
            borderRadius: BorderRadius.circular(10),
            child: InkWell(
              borderRadius: BorderRadius.circular(10),
              onTap: onCopy,
              child: const Padding(
                padding: EdgeInsets.all(9),
                child: Icon(Icons.copy_rounded, size: 17, color: orange),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _bankTransferDetail() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _sectionLabel("Transfer ke salah satu rekening berikut"),
        ..._banks.map((bank) => _detailRow(
              title: bank['nama']!,
              value: bank['rekening']!,
              subtitle: 'a.n. ${bank['atasNama']}',
              onCopy: () => _copy(bank['rekening']!, 'Nomor rekening'),
            )),
      ],
    );
  }

  Widget _eWalletDetail() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _sectionLabel("Kirim ke salah satu e-wallet berikut"),
        ..._ewallets.map((wallet) => _detailRow(
              title: wallet['nama']!,
              value: wallet['nomor']!,
              onCopy: () => _copy(wallet['nomor']!, 'Nomor ${wallet['nama']}'),
            )),
      ],
    );
  }

  Widget _qrisDetail() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _sectionLabel("Scan kode QRIS berikut"),
        Center(
          child: Container(
            width: 220,
            height: 220,
            decoration: BoxDecoration(
              border: Border.all(color: amber, width: 1.5),
              borderRadius: BorderRadius.circular(18),
              color: amber.withValues(alpha: 0.08),
            ),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.qr_code_2_rounded, size: 110, color: navyDark),
                const SizedBox(height: 8),
                Text(
                  'Kode QRIS (simulasi)',
                  style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: 10),
        Center(
          child: Text(
            'Gunakan aplikasi m-banking atau e-wallet mana pun untuk scan.',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 12.5, color: Colors.grey.shade600),
          ),
        ),
      ],
    );
  }

  Widget _detailForMetode() {
    switch (metodeBayar) {
      case "Transfer Bank":
        return _bankTransferDetail();
      case "E-Wallet":
        return _eWalletDetail();
      case "QRIS":
        return _qrisDetail();
      default:
        return Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: teal.withValues(alpha: 0.08),
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: teal.withValues(alpha: 0.3)),
          ),
          child: Row(
            children: [
              const Icon(Icons.storefront_rounded, color: teal),
              const SizedBox(width: 10),
              const Expanded(
                child: Text(
                  "Pembayaran dilakukan langsung di lokasi saat pengambilan mobil.",
                  style: TextStyle(fontSize: 12.5, color: navyDark),
                ),
              ),
            ],
          ),
        );
    }
  }

  Widget _metodeSelector() {
    return Wrap(
      spacing: 10,
      runSpacing: 10,
      children: _metodeOptions.map((opt) {
        final value = opt['value'] as String;
        final active = metodeBayar == value;
        return GestureDetector(
          onTap: () {
            setState(() {
              metodeBayar = value;
              _buktiTransferBytes = null; // reset bukti kalau ganti metode
              _buktiTransferFileName = null;
            });
          },
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 180),
            width: 108,
            padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 10),
            decoration: BoxDecoration(
              color: active ? orange.withValues(alpha: 0.08) : const Color(0xFFF6F6F6),
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: active ? orange : Colors.grey.shade300, width: active ? 1.5 : 1),
            ),
            child: Column(
              children: [
                Container(
                  width: 32,
                  height: 32,
                  decoration: BoxDecoration(
                    color: active ? orange : Colors.white,
                    borderRadius: BorderRadius.circular(10),
                    border: active ? null : Border.all(color: Colors.grey.shade300),
                  ),
                  child: Icon(opt['icon'] as IconData,
                      size: 16, color: active ? Colors.white : Colors.grey.shade500),
                ),
                const SizedBox(height: 8),
                Text(
                  opt['label'] as String,
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 11.5,
                    fontWeight: FontWeight.bold,
                    color: active ? navyDark : Colors.grey.shade700,
                  ),
                ),
              ],
            ),
          ),
        );
      }).toList(),
    );
  }

  Widget _buktiTransferSection() {
    if (!_perluBuktiTransfer) return const SizedBox.shrink();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const SizedBox(height: 4),
        _sectionLabel("Upload Bukti Transfer"),
        InkWell(
          onTap: _pilihBuktiTransfer,
          borderRadius: BorderRadius.circular(14),
          child: Container(
            width: double.infinity,
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: _buktiTransferBytes == null ? const Color(0xFFF6F6F6) : Colors.white,
              border: Border.all(
                color: _buktiTransferBytes == null ? Colors.grey.shade300 : teal,
                width: _buktiTransferBytes == null ? 1 : 1.5,
              ),
              borderRadius: BorderRadius.circular(14),
            ),
            child: _buktiTransferBytes == null
                ? Column(
                    children: [
                      Container(
                        width: 44,
                        height: 44,
                        decoration: BoxDecoration(
                          color: teal.withValues(alpha: 0.12),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Icon(Icons.upload_file_rounded, color: teal, size: 22),
                      ),
                      const SizedBox(height: 10),
                      Text(
                        'Tap untuk pilih foto bukti transfer',
                        style: TextStyle(fontSize: 12.5, color: Colors.grey.shade600),
                      ),
                    ],
                  )
                : Column(
                    children: [
                      ClipRRect(
                        borderRadius: BorderRadius.circular(10),
                        // Menggunakan Image.memory agar aman di Flutter Web
                        child: Image.memory(_buktiTransferBytes!, height: 160, fit: BoxFit.cover, width: double.infinity),
                      ),
                      const SizedBox(height: 10),
                      Material(
                        color: teal.withValues(alpha: 0.12),
                        borderRadius: BorderRadius.circular(10),
                        child: InkWell(
                          borderRadius: BorderRadius.circular(10),
                          onTap: _pilihBuktiTransfer,
                          child: const Padding(
                            padding: EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Icon(Icons.refresh_rounded, size: 16, color: teal),
                                SizedBox(width: 6),
                                Text('Ganti foto',
                                    style: TextStyle(fontSize: 12.5, color: teal, fontWeight: FontWeight.w600)),
                              ],
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
          ),
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: bgTop,
      appBar: AppBar(
        title: const Text(
          "Pembayaran",
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
              // Kartu total pembayaran, gaya hero seperti Beranda
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [navyDark, navyLight],
                  ),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Container(
                          width: 34,
                          height: 34,
                          decoration: BoxDecoration(
                            color: amber,
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: const Icon(Icons.receipt_long_rounded, color: navyDark, size: 18),
                        ),
                        const SizedBox(width: 10),
                        const Text(
                          'Total Pembayaran',
                          style: TextStyle(color: Colors.white70, fontSize: 13),
                        ),
                      ],
                    ),
                    const SizedBox(height: 14),
                    Text(
                      "Rp ${widget.totalBayar.toStringAsFixed(0)}",
                      style: const TextStyle(
                        fontSize: 30,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 20),

              _card(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      "Metode Pembayaran",
                      style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: navyDark),
                    ),
                    const SizedBox(height: 14),
                    _metodeSelector(),
                  ],
                ),
              ),

              _card(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _detailForMetode(),
                    _buktiTransferSection(),
                  ],
                ),
              ),

              const SizedBox(height: 8),

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
                    onTap: loading ? null : bayar,
                    child: Center(
                      child: loading
                          ? const SizedBox(
                              width: 22,
                              height: 22,
                              child: CircularProgressIndicator(strokeWidth: 2.4, color: Colors.white),
                            )
                          : const Text(
                              "Bayar Sekarang",
                              style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: Colors.white),
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