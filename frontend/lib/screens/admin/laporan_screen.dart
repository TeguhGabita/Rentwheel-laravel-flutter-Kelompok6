import 'package:flutter/material.dart';
import 'package:pdf/pdf.dart';
import 'package:pdf/widgets.dart' as pw;
import 'package:printing/printing.dart';
import '../../services/api_service.dart';

class LaporanScreen extends StatefulWidget {
  const LaporanScreen({super.key});

  @override
  State<LaporanScreen> createState() => _LaporanScreenState();
}

class _LaporanScreenState extends State<LaporanScreen> {
  static const Color primaryYellow = Color(0xFFFBBF24);
  static const Color darkNavy = Color(0xFF0F172A);

  // Daftar status booking yang valid, dipakai untuk validasi & dropdown pilihan.
  static const List<String> statusOptions = ['dipesan', 'berjalan', 'selesai', 'batal'];

  // Daftar status pembayaran yang valid (sesuai kolom status_bayar di tabel pembayarans).
  static const List<String> statusBayarOptions = ['pending', 'lunas', 'ditolak'];

  bool loading = true;
  int totalPendapatan = 0;
  int totalBooking = 0;
  List<Map<String, dynamic>> transaksiList = [];

  // id transaksi yang sedang diproses update status-nya (untuk loading indicator per-item).
  dynamic _updatingId;
  dynamic _updatingPembayaranId;

  @override
  void initState() {
    super.initState();
    fetchLaporan();
  }

  int _asInt(dynamic value) => int.tryParse(value?.toString() ?? '0') ?? 0;

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

  Future<void> fetchLaporan() async {
    setState(() => loading = true);

    final result = await ApiService.getLaporan();

    setState(() {
      if (result['success'] == true && result['data'] != null) {
        final data = Map<String, dynamic>.from(result['data']);
        totalPendapatan = _asInt(data['total_pendapatan']);
        totalBooking = _asInt(data['total_booking']);
        transaksiList = List<Map<String, dynamic>>.from(data['transaksi'] ?? []);
      }
      loading = false;
    });
  }

  // FIX: nilai status booking yang benar adalah 'dipesan', 'berjalan',
  // 'selesai', 'batal' (bukan 'dibatalkan'). Sebelumnya 'dipesan' dan 'batal'
  // tidak ter-cover sehingga selalu jatuh ke warna abu-abu default.
  Color _statusColor(String status) {
    switch (status.toLowerCase()) {
      case 'dipesan':
        return Colors.blueGrey;
      case 'berjalan':
        return primaryYellow;
      case 'selesai':
        return Colors.green;
      case 'batal':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  String _statusLabel(String status) {
    switch (status.toLowerCase()) {
      case 'dipesan':
        return 'Dipesan';
      case 'berjalan':
        return 'Berjalan';
      case 'selesai':
        return 'Selesai';
      case 'batal':
        return 'Batal';
      default:
        return status;
    }
  }

  // Warna & label untuk status pembayaran (status_bayar: pending / lunas / ditolak).
  Color _statusBayarColor(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return primaryYellow;
      case 'lunas':
        return Colors.green;
      case 'ditolak':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  String _statusBayarLabel(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return 'Menunggu Konfirmasi';
      case 'lunas':
        return 'Lunas';
      case 'ditolak':
        return 'Ditolak';
      default:
        return status.isEmpty ? '-' : status;
    }
  }

  /// Ambil ID booking dari satu baris transaksi. Backend disarankan
  /// mengirim field 'booking_id' di setiap item transaksi laporan;
  /// fallback ke 'id' kalau backend memakai nama field itu.
  dynamic _bookingIdOf(Map<String, dynamic> trx) {
    return trx['booking_id'] ?? trx['id'];
  }

  /// Tampilkan dialog pilihan status, lalu panggil API untuk update kalau
  /// admin memilih status baru.
  Future<void> _ubahStatusDialog(Map<String, dynamic> trx) async {
    final bookingId = _bookingIdOf(trx);
    if (bookingId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('ID booking tidak ditemukan pada data ini.')),
      );
      return;
    }

    final currentStatus = (trx['status']?.toString() ?? '').toLowerCase();

    final selected = await showModalBottomSheet<String>(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
      ),
      builder: (sheetContext) {
        return SafeArea(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Padding(
                padding: EdgeInsets.fromLTRB(20, 20, 20, 8),
                child: Align(
                  alignment: Alignment.centerLeft,
                  child: Text(
                    'Ubah Status Booking',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                ),
              ),
              ...statusOptions.map((status) {
                final isSelected = status == currentStatus;
                return ListTile(
                  leading: Container(
                    width: 12,
                    height: 12,
                    decoration: BoxDecoration(
                      color: _statusColor(status),
                      shape: BoxShape.circle,
                    ),
                  ),
                  title: Text(_statusLabel(status)),
                  trailing: isSelected
                      ? const Icon(Icons.check_circle_rounded, color: primaryYellow)
                      : null,
                  onTap: () => Navigator.pop(sheetContext, status),
                );
              }),
              const SizedBox(height: 8),
            ],
          ),
        );
      },
    );

    if (selected == null || selected == currentStatus) return;

    setState(() => _updatingId = bookingId);

    final result = await ApiService.updateBookingStatus(bookingId, selected);

    if (!mounted) return;

    setState(() => _updatingId = null);

    if (result['success'] == true) {
      setState(() {
        trx['status'] = selected;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Status booking diubah ke "${_statusLabel(selected)}".')),
      );
      // Refresh total pendapatan/booking karena bisa berubah tergantung status
      // (misal booking yang dibatalkan tidak lagi dihitung pendapatan).
      fetchLaporan();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? 'Gagal mengubah status booking.')),
      );
    }
  }

  /// Tampilkan dialog pilihan status pembayaran (acc / tolak), lalu panggil
  /// API untuk update. Ditandai berbeda dari status booking supaya admin
  /// bisa mengelola keduanya secara independen.
  Future<void> _ubahStatusBayarDialog(Map<String, dynamic> trx) async {
    final pembayaranId = trx['pembayaran_id'];

    if (pembayaranId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Belum ada data pembayaran untuk booking ini.')),
      );
      return;
    }

    final currentStatus = (trx['status_bayar']?.toString() ?? '').toLowerCase();

    final selected = await showModalBottomSheet<String>(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
      ),
      builder: (sheetContext) {
        return SafeArea(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Padding(
                padding: EdgeInsets.fromLTRB(20, 20, 20, 8),
                child: Align(
                  alignment: Alignment.centerLeft,
                  child: Text(
                    'Ubah Status Pembayaran',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                ),
              ),
              ...statusBayarOptions.map((status) {
                final isSelected = status == currentStatus;
                return ListTile(
                  leading: Container(
                    width: 12,
                    height: 12,
                    decoration: BoxDecoration(
                      color: _statusBayarColor(status),
                      shape: BoxShape.circle,
                    ),
                  ),
                  title: Text(_statusBayarLabel(status)),
                  trailing: isSelected
                      ? const Icon(Icons.check_circle_rounded, color: primaryYellow)
                      : null,
                  onTap: () => Navigator.pop(sheetContext, status),
                );
              }),
              const SizedBox(height: 8),
            ],
          ),
        );
      },
    );

    if (selected == null || selected == currentStatus) return;

    setState(() => _updatingPembayaranId = pembayaranId);

    final result = await ApiService.updatePembayaranStatus(pembayaranId, selected);

    if (!mounted) return;

    setState(() => _updatingPembayaranId = null);

    if (result['success'] == true) {
      setState(() {
        trx['status_bayar'] = selected;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Status pembayaran diubah ke "${_statusBayarLabel(selected)}".')),
      );
      // Refresh total pendapatan karena hanya pembayaran 'lunas' yang dihitung.
      fetchLaporan();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? 'Gagal mengubah status pembayaran.')),
      );
    }
  }

  /// Generate PDF laporan lalu tampilkan dialog print/preview browser
  /// (di web ini otomatis buka tab baru berisi PDF yang bisa di-print
  /// atau di-download/save-as oleh user).
  Future<void> cetakLaporanPdf() async {
    final now = DateTime.now();
    final tanggalCetak =
        '${now.day.toString().padLeft(2, '0')}/${now.month.toString().padLeft(2, '0')}/${now.year} '
        '${now.hour.toString().padLeft(2, '0')}:${now.minute.toString().padLeft(2, '0')}';

    final doc = pw.Document();

    doc.addPage(
      pw.MultiPage(
        pageFormat: PdfPageFormat.a4,
        margin: const pw.EdgeInsets.all(32),
        header: (context) => pw.Column(
          crossAxisAlignment: pw.CrossAxisAlignment.start,
          children: [
            pw.Text(
              'Laporan RentWheel',
              style: pw.TextStyle(fontSize: 20, fontWeight: pw.FontWeight.bold),
            ),
            pw.SizedBox(height: 4),
            pw.Text('Dicetak pada: $tanggalCetak', style: const pw.TextStyle(fontSize: 10)),
            pw.SizedBox(height: 16),
          ],
        ),
        build: (context) => [
          pw.Row(
            mainAxisAlignment: pw.MainAxisAlignment.spaceBetween,
            children: [
              pw.Expanded(
                child: pw.Container(
                  padding: const pw.EdgeInsets.all(12),
                  decoration: pw.BoxDecoration(
                    border: pw.Border.all(color: PdfColors.grey300),
                    borderRadius: pw.BorderRadius.circular(6),
                  ),
                  child: pw.Column(
                    crossAxisAlignment: pw.CrossAxisAlignment.start,
                    children: [
                      pw.Text('Total Pendapatan', style: const pw.TextStyle(fontSize: 10, color: PdfColors.grey700)),
                      pw.SizedBox(height: 4),
                      pw.Text(
                        _rupiah(totalPendapatan),
                        style: pw.TextStyle(fontSize: 14, fontWeight: pw.FontWeight.bold),
                      ),
                    ],
                  ),
                ),
              ),
              pw.SizedBox(width: 12),
              pw.Expanded(
                child: pw.Container(
                  padding: const pw.EdgeInsets.all(12),
                  decoration: pw.BoxDecoration(
                    border: pw.Border.all(color: PdfColors.grey300),
                    borderRadius: pw.BorderRadius.circular(6),
                  ),
                  child: pw.Column(
                    crossAxisAlignment: pw.CrossAxisAlignment.start,
                    children: [
                      pw.Text('Total Booking', style: const pw.TextStyle(fontSize: 10, color: PdfColors.grey700)),
                      pw.SizedBox(height: 4),
                      pw.Text(
                        totalBooking.toString(),
                        style: pw.TextStyle(fontSize: 14, fontWeight: pw.FontWeight.bold),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
          pw.SizedBox(height: 24),
          pw.Text(
            'Daftar Transaksi',
            style: pw.TextStyle(fontSize: 13, fontWeight: pw.FontWeight.bold),
          ),
          pw.SizedBox(height: 8),
          if (transaksiList.isEmpty)
            pw.Padding(
              padding: const pw.EdgeInsets.symmetric(vertical: 20),
              child: pw.Text('Belum ada data transaksi', style: const pw.TextStyle(color: PdfColors.grey600)),
            )
          else
            pw.Table(
              border: pw.TableBorder.all(color: PdfColors.grey300),
              columnWidths: const {
                0: pw.FlexColumnWidth(2),
                1: pw.FlexColumnWidth(1.8),
                2: pw.FlexColumnWidth(1.3),
                3: pw.FlexColumnWidth(1.4),
                4: pw.FlexColumnWidth(1.3),
                5: pw.FlexColumnWidth(1.5),
              },
              children: [
                pw.TableRow(
                  decoration: const pw.BoxDecoration(color: PdfColors.grey200),
                  children: [
                    _pdfCell('Mobil', bold: true),
                    _pdfCell('Pelanggan', bold: true),
                    _pdfCell('Tanggal', bold: true),
                    _pdfCell('Total Bayar', bold: true),
                    _pdfCell('Status', bold: true),
                    _pdfCell('Pembayaran', bold: true),
                  ],
                ),
                ...transaksiList.map((trx) {
                  final status = trx['status']?.toString() ?? '-';
                  final statusBayar = trx['status_bayar']?.toString() ?? '-';
                  return pw.TableRow(
                    children: [
                      _pdfCell(trx['nama_mobil']?.toString() ?? '-'),
                      _pdfCell(trx['nama_pelanggan']?.toString() ?? '-'),
                      _pdfCell(trx['tanggal']?.toString() ?? '-'),
                      _pdfCell(_rupiah(_asInt(trx['total_bayar']))),
                      _pdfCell(status),
                      _pdfCell(_statusBayarLabel(statusBayar)),
                    ],
                  );
                }),
              ],
            ),
        ],
      ),
    );

    await Printing.layoutPdf(
      onLayout: (format) async => doc.save(),
      name: 'laporan_rentwheel_${now.millisecondsSinceEpoch}.pdf',
    );
  }

  pw.Widget _pdfCell(String text, {bool bold = false}) {
    return pw.Padding(
      padding: const pw.EdgeInsets.symmetric(horizontal: 6, vertical: 6),
      child: pw.Text(
        text,
        style: pw.TextStyle(
          fontSize: 9,
          fontWeight: bold ? pw.FontWeight.bold : pw.FontWeight.normal,
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF4F5F7),
      appBar: AppBar(
        backgroundColor: darkNavy,
        title: const Text('Laporan', style: TextStyle(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          IconButton(
            tooltip: 'Cetak Laporan (PDF)',
            icon: const Icon(Icons.print_outlined, color: Colors.white),
            onPressed: loading ? null : cetakLaporanPdf,
          ),
        ],
      ),
      body: loading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: fetchLaporan,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    LayoutBuilder(
                      builder: (context, constraints) {
                        final isWide = constraints.maxWidth >= 600;
                        final pendapatanCard = _summaryCard(
                          label: 'Total Pendapatan',
                          value: _rupiah(totalPendapatan),
                          icon: Icons.attach_money_outlined,
                          color: Colors.green,
                          bgColor: const Color(0xFFE1F6EC),
                        );
                        final bookingCard = _summaryCard(
                          label: 'Total Booking',
                          value: totalBooking.toString(),
                          icon: Icons.receipt_long_outlined,
                          color: Colors.purple,
                          bgColor: const Color(0xFFEFE5FB),
                        );

                        if (isWide) {
                          return Row(
                            children: [
                              Expanded(child: pendapatanCard),
                              const SizedBox(width: 16),
                              Expanded(child: bookingCard),
                            ],
                          );
                        }

                        return Column(
                          children: [
                            pendapatanCard,
                            const SizedBox(height: 16),
                            bookingCard,
                          ],
                        );
                      },
                    ),
                    const SizedBox(height: 24),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'Daftar Transaksi',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                        ),
                        TextButton.icon(
                          onPressed: loading ? null : cetakLaporanPdf,
                          icon: const Icon(Icons.picture_as_pdf_outlined, size: 18),
                          label: const Text('Cetak PDF'),
                          style: TextButton.styleFrom(foregroundColor: darkNavy),
                        ),
                      ],
                    ),
                    Padding(
                      padding: const EdgeInsets.only(top: 4, bottom: 8),
                      child: Text(
                        'Tekan badge status untuk mengubahnya (status booking maupun status pembayaran).',
                        style: TextStyle(fontSize: 11.5, color: Colors.grey.shade500, fontStyle: FontStyle.italic),
                      ),
                    ),
                    const SizedBox(height: 4),
                    transaksiList.isEmpty
                        ? Padding(
                            padding: const EdgeInsets.symmetric(vertical: 60),
                            child: Center(
                              child: Text(
                                'Belum ada data transaksi',
                                style: TextStyle(color: Colors.grey.shade600),
                              ),
                            ),
                          )
                        : ListView.builder(
                            shrinkWrap: true,
                            physics: const NeverScrollableScrollPhysics(),
                            itemCount: transaksiList.length,
                            itemBuilder: (context, index) {
                              final trx = transaksiList[index];
                              final status = trx['status']?.toString() ?? '-';
                              final statusBayar = trx['status_bayar']?.toString() ?? '-';
                              final bookingId = _bookingIdOf(trx);
                              final pembayaranId = trx['pembayaran_id'];
                              final isUpdatingThis = _updatingId == bookingId;
                              final isUpdatingBayarThis =
                                  _updatingPembayaranId == pembayaranId && pembayaranId != null;

                              return Card(
                                margin: const EdgeInsets.only(bottom: 12),
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                child: Padding(
                                  padding: const EdgeInsets.all(12),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Row(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Expanded(
                                            child: Column(
                                              crossAxisAlignment: CrossAxisAlignment.start,
                                              children: [
                                                Text(
                                                  trx['nama_mobil']?.toString() ?? '-',
                                                  style: const TextStyle(fontWeight: FontWeight.bold),
                                                ),
                                                Padding(
                                                  padding: const EdgeInsets.only(top: 4),
                                                  child: Text(
                                                    '${trx['nama_pelanggan'] ?? '-'} • ${trx['tanggal'] ?? '-'}',
                                                  ),
                                                ),
                                              ],
                                            ),
                                          ),
                                          Text(
                                            _rupiah(_asInt(trx['total_bayar'])),
                                            style: const TextStyle(fontWeight: FontWeight.bold),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 10),
                                      // Baris badge: status booking & status pembayaran berdampingan.
                                      Wrap(
                                        spacing: 8,
                                        runSpacing: 8,
                                        children: [
                                          // Badge status BOOKING
                                          InkWell(
                                            onTap: isUpdatingThis ? null : () => _ubahStatusDialog(trx),
                                            borderRadius: BorderRadius.circular(6),
                                            child: Container(
                                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                              decoration: BoxDecoration(
                                                color: _statusColor(status).withValues(alpha: 0.12),
                                                borderRadius: BorderRadius.circular(6),
                                              ),
                                              child: isUpdatingThis
                                                  ? const SizedBox(
                                                      width: 12,
                                                      height: 12,
                                                      child: CircularProgressIndicator(strokeWidth: 1.5),
                                                    )
                                                  : Row(
                                                      mainAxisSize: MainAxisSize.min,
                                                      children: [
                                                        Icon(Icons.local_shipping_outlined,
                                                            size: 11, color: _statusColor(status)),
                                                        const SizedBox(width: 3),
                                                        Text(
                                                          _statusLabel(status),
                                                          style: TextStyle(
                                                            color: _statusColor(status),
                                                            fontSize: 11,
                                                            fontWeight: FontWeight.w600,
                                                          ),
                                                        ),
                                                        const SizedBox(width: 3),
                                                        Icon(Icons.edit_outlined,
                                                            size: 11, color: _statusColor(status)),
                                                      ],
                                                    ),
                                            ),
                                          ),
                                          // Badge status PEMBAYARAN
                                          InkWell(
                                            onTap: isUpdatingBayarThis ? null : () => _ubahStatusBayarDialog(trx),
                                            borderRadius: BorderRadius.circular(6),
                                            child: Container(
                                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                              decoration: BoxDecoration(
                                                color: _statusBayarColor(statusBayar).withValues(alpha: 0.12),
                                                borderRadius: BorderRadius.circular(6),
                                              ),
                                              child: isUpdatingBayarThis
                                                  ? const SizedBox(
                                                      width: 12,
                                                      height: 12,
                                                      child: CircularProgressIndicator(strokeWidth: 1.5),
                                                    )
                                                  : Row(
                                                      mainAxisSize: MainAxisSize.min,
                                                      children: [
                                                        Icon(Icons.payments_outlined,
                                                            size: 11, color: _statusBayarColor(statusBayar)),
                                                        const SizedBox(width: 3),
                                                        Text(
                                                          _statusBayarLabel(statusBayar),
                                                          style: TextStyle(
                                                            color: _statusBayarColor(statusBayar),
                                                            fontSize: 11,
                                                            fontWeight: FontWeight.w600,
                                                          ),
                                                        ),
                                                        const SizedBox(width: 3),
                                                        Icon(Icons.edit_outlined,
                                                            size: 11, color: _statusBayarColor(statusBayar)),
                                                      ],
                                                    ),
                                            ),
                                          ),
                                        ],
                                      ),
                                    ],
                                  ),
                                ),
                              );
                            },
                          ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _summaryCard({
    required String label,
    required String value,
    required IconData icon,
    required Color color,
    required Color bgColor,
  }) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Row(
        children: [
          Container(
            width: 44,
            height: 44,
            decoration: BoxDecoration(
              color: bgColor,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: color, size: 22),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label, style: TextStyle(fontSize: 13, color: Colors.grey.shade600)),
                const SizedBox(height: 2),
                Text(value, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}