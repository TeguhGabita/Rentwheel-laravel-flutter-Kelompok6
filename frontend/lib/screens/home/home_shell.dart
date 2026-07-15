import 'package:flutter/material.dart';
import 'home_screen.dart';
import '../profil/riwayat_booking_screen.dart';
import '../profil/profil_screen.dart';

class HomeShell extends StatefulWidget {
  const HomeShell({super.key});

  @override
  State<HomeShell> createState() => _HomeShellState();
}

class _HomeShellState extends State<HomeShell> {
  int _index = 0;

  static const Color orange = Color(0xFFFB8500);

  final List<Widget> _pages = const [
    HomeScreen(),
    RiwayatBookingScreen(),
    ProfilScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      // IndexedStack: setiap tab tetap "hidup" (scroll position, data
      // yang sudah di-fetch tidak hilang) saat pindah tab.
      body: IndexedStack(
        index: _index,
        children: _pages,
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: (i) => setState(() => _index = i),
        indicatorColor: orange.withValues(alpha: 0.15),
        backgroundColor: Colors.white,
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.home_outlined),
            selectedIcon: Icon(Icons.home_rounded, color: orange),
            label: 'Beranda',
          ),
          NavigationDestination(
            icon: Icon(Icons.receipt_long_outlined),
            selectedIcon: Icon(Icons.receipt_long_rounded, color: orange),
            label: 'Riwayat',
          ),
          NavigationDestination(
            icon: Icon(Icons.person_outline_rounded),
            selectedIcon: Icon(Icons.person_rounded, color: orange),
            label: 'Profil',
          ),
        ],
      ),
    );
  }
}