import 'package:flutter/material.dart';

import 'screens/splash/splash_screen.dart';
import 'screens/auth/login_screen.dart';
import 'screens/auth/register_screen.dart';
import 'screens/home/home_shell.dart'; 
import 'screens/admin/admin_dashboard_screen.dart';
import 'screens/admin/notifikasi_admin_screen.dart';
void main() {
  runApp(const RentWheelApp());
}

class RentWheelApp extends StatelessWidget {
  const RentWheelApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'RentWheel',

      theme: ThemeData(
        useMaterial3: true,
        colorSchemeSeed: const Color(0xFFFBBF24),
      ),

      initialRoute: '/',

      routes: {
      '/': (_) => const SplashScreen(),
      '/login': (_) => const LoginScreen(),
      '/register': (_) => const RegisterScreen(),
      '/home': (_) => const HomeShell(),
      '/notifications': (_) => const NotifikasiScreen(),
      },

      // '/admin' butuh argumen (adminEmail, adminName) yang baru ada
      // setelah proses login, jadi tidak bisa didaftarkan di `routes` statis.
      // Kita tangani lewat onGenerateRoute supaya bisa terima arguments.
      onGenerateRoute: (settings) {
        if (settings.name == '/admin') {
          final args = settings.arguments as Map<String, dynamic>? ?? {};
          return MaterialPageRoute(
            builder: (_) => AdminDashboardScreen(
              adminEmail: args['adminEmail'] ?? '',
              adminName: args['adminName'] ?? '',
            ),
          );
        }
        return null;
      },
    );
  }
}