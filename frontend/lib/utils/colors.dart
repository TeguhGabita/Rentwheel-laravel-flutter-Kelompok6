import 'package:flutter/material.dart';

class AppColors {
  AppColors._(); // biar tidak bisa di-instantiate

  // Warna utama brand (dipakai di colorSchemeSeed main.dart)
  static const Color primary = Color(0xFFFBBF24); // amber/kuning RentWheel
  static const Color primaryDark = Colors.amber;

  // Background (tema gelap admin dashboard)
  static const Color background = Color(0xFF0E0E0E);
  static const Color surface = Color(0xFF1A1A1A);
  static const Color surfaceAlt = Color(0xFF161616);

  // Border & divider
  static const Color border = Colors.white12;

  // Teks
  static const Color textPrimary = Colors.white;
  static const Color textSecondary = Colors.white60;
  static const Color textDisabled = Colors.white38;

  // Status
  static const Color danger = Colors.redAccent;
  static const Color success = Colors.green;
  static const Color warning = Colors.orange;

  // Helper warna transparan (ganti pola .withOpacity yang deprecated)
  static Color primaryWithAlpha(double alpha) =>
      primaryDark.withValues(alpha: alpha);
}