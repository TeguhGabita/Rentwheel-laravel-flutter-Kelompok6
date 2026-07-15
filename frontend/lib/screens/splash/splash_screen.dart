import 'dart:async';
import 'package:flutter/material.dart';
import '../auth/login_screen.dart';
import '../home/home_shell.dart';
import '../admin/admin_dashboard_screen.dart';
import '../../services/api_service.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _checkSession();
  }

  Future<void> _checkSession() async {
    // Kasih jeda 2 detik supaya splash tetap kelihatan (bukan cuma delay teknis,
    // tapi juga waktu untuk cek token tersimpan di local storage).
    final delay = Future.delayed(const Duration(seconds: 2));

    final token = await ApiService.getToken();
    final user = await ApiService.getSavedUser();

    await delay;

    if (!mounted) return;

    // Belum pernah login / token kosong -> ke halaman Login
    if (token == null || user == null) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => const LoginScreen()),
      );
      return;
    }

    // Sudah ada sesi tersimpan -> langsung masuk sesuai role, skip login
    final role = user['role'];

    if (role == 'admin') {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (_) => AdminDashboardScreen(
            adminEmail: user['email'] ?? '',
            adminName: user['name'] ?? '',
          ),
        ),
      );
    } else {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => const HomeShell()),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF141414),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 90,
              height: 90,
              decoration: BoxDecoration(
                color: const Color(0xFFFBBF24),
                borderRadius: BorderRadius.circular(20),
              ),
              child: const Icon(
                Icons.directions_car_filled_rounded,
                size: 48,
                color: Colors.black87,
              ),
            ),
            const SizedBox(height: 20),
            const Text(
              "RentWheel",
              style: TextStyle(
                color: Colors.white,
                fontSize: 30,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
      ),
    );
  }
}