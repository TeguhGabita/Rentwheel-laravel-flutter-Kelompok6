class Currency {
  Currency._();

  /// Format angka jadi "Rp 150.000" (dengan titik pemisah ribuan, tanpa desimal).
  /// Contoh: Currency.rupiah(150000) => "Rp 150.000"
  static String rupiah(num value) {
    final str = value.toInt().toString();
    final buffer = StringBuffer();

    for (int i = 0; i < str.length; i++) {
      final posFromRight = str.length - i;
      buffer.write(str[i]);
      if (posFromRight > 1 && posFromRight % 3 == 1) {
        buffer.write('.');
      }
    }

    return 'Rp ${buffer.toString()}';
  }

  /// Sama seperti [rupiah] tapi dengan akhiran "/hari" untuk harga sewa.
  /// Contoh: Currency.rupiahPerHari(150000) => "Rp 150.000/hari"
  static String rupiahPerHari(num value) {
    return '${rupiah(value)}/hari';
  }

  /// Parse string "Rp 150.000" atau "150000" balik jadi angka.
  /// Berguna kalau ambil input dari TextField yang sudah diformat.
  static double parse(String formatted) {
    final cleaned = formatted.replaceAll(RegExp(r'[^\d]'), '');
    return double.tryParse(cleaned) ?? 0;
  }
}