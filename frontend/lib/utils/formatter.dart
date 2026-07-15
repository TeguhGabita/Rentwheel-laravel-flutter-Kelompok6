class Formatters {
  Formatters._();

  static const List<String> _namaBulan = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
  ];

  /// Ubah "2026-07-11" (format dari Laravel/MySQL) jadi "11 Juli 2026".
  /// Kalau format tanggal tidak valid, kembalikan string aslinya.
  static String tanggalIndo(String? isoDate) {
    if (isoDate == null || isoDate.isEmpty) return '-';

    try {
      final date = DateTime.parse(isoDate);
      return '${date.day} ${_namaBulan[date.month - 1]} ${date.year}';
    } catch (e) {
      return isoDate;
    }
  }

  /// Ubah "2026-07-11" jadi "11/07/2026".
  static String tanggalSlash(String? isoDate) {
    if (isoDate == null || isoDate.isEmpty) return '-';

    try {
      final date = DateTime.parse(isoDate);
      final day = date.day.toString().padLeft(2, '0');
      final month = date.month.toString().padLeft(2, '0');
      return '$day/$month/${date.year}';
    } catch (e) {
      return isoDate;
    }
  }

  /// Ubah DateTime jadi format "2026-07-11" (buat dikirim ke Laravel).
  static String toApiDate(DateTime date) {
    final month = date.month.toString().padLeft(2, '0');
    final day = date.day.toString().padLeft(2, '0');
    return '${date.year}-$month-$day';
  }

  /// Hitung selisih hari antara dua tanggal (buat hitung total harga sewa).
  /// Contoh: tanggal_mulai "2026-07-11", tanggal_selesai "2026-07-14" => 3 hari.
  static int selisihHari(String tanggalMulai, String tanggalSelesai) {
    try {
      final mulai = DateTime.parse(tanggalMulai);
      final selesai = DateTime.parse(tanggalSelesai);
      return selesai.difference(mulai).inDays;
    } catch (e) {
      return 0;
    }
  }

  /// Kapitalisasi huruf pertama tiap kata. "transfer bank" => "Transfer Bank".
  static String capitalize(String text) {
    if (text.isEmpty) return text;
    return text.split(' ').map((word) {
      if (word.isEmpty) return word;
      return word[0].toUpperCase() + word.substring(1).toLowerCase();
    }).join(' ');
  }
}