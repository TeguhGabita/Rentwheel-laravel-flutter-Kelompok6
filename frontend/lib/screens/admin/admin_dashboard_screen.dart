import 'package:flutter/material.dart';

/// Halaman dashboard admin RentWheel.
/// Menu CRUD di bawah ini masih placeholder — belum ada logic/implementasi,
/// cuma navigasi ke halaman kosong sebagai penanda tempat.
///
/// [adminName] dan [adminEmail] diisi dari data user yang sedang login
/// (misal hasil response GET /api/auth/me), bukan nilai tetap.
class AdminDashboardScreen extends StatelessWidget {
  final String adminName;
  final String adminEmail;

  const AdminDashboardScreen({
    super.key,
    required this.adminName,
    required this.adminEmail,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0E0E0E),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildHeader(),
              const SizedBox(height: 20),
              _buildAdminProfileCard(context),
              const SizedBox(height: 24),
              _buildStatsRow(),
              const SizedBox(height: 24),
              _buildSectionLabel('Kelola data'),
              const SizedBox(height: 8),
              _buildCrudGrid(context),
              const SizedBox(height: 24),
              _buildSectionLabel('Lainnya'),
              const SizedBox(height: 8),
              _buildOtherMenu(context),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        const Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'RentWheel',
              style: TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.w600,
                color: Colors.white,
              ),
            ),
            SizedBox(height: 2),
            Text(
              'Dashboard admin',
              style: TextStyle(fontSize: 13, color: Colors.white60),
            ),
          ],
        ),
        const Icon(Icons.notifications_outlined, color: Colors.white60),
      ],
    );
  }

  Widget _buildAdminProfileCard(BuildContext context) {
    return InkWell(
      borderRadius: BorderRadius.circular(12),
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => AdminProfileScreen(
              adminName: adminName,
              adminEmail: adminEmail,
            ),
          ),
        );
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        decoration: BoxDecoration(
          color: const Color(0xFF1A1A1A),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: Colors.white12),
        ),
        child: Row(
          children: [
            CircleAvatar(
              radius: 24,
              backgroundColor: Colors.amber.withOpacity(0.2),
              child: Text(
                adminName.isNotEmpty ? adminName[0].toUpperCase() : '?',
                style: const TextStyle(
                  color: Colors.amber,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    adminName,
                    style: const TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w600,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    adminEmail,
                    style: const TextStyle(fontSize: 13, color: Colors.white60),
                  ),
                ],
              ),
            ),
            const Icon(Icons.chevron_right, color: Colors.white38),
          ],
        ),
      ),
    );
  }

  Widget _buildStatsRow() {
    return Row(
      children: [
        Expanded(child: _statCard('Total mobil', '5')),
        const SizedBox(width: 12),
        Expanded(child: _statCard('Booking aktif', '0')),
      ],
    );
  }

  Widget _statCard(String label, String value) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF161616),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: const TextStyle(fontSize: 13, color: Colors.white60)),
          const SizedBox(height: 4),
          Text(
            value,
            style: const TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.w600,
              color: Colors.white,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionLabel(String text) {
    return Text(text, style: const TextStyle(fontSize: 13, color: Colors.white60));
  }

  Widget _buildCrudGrid(BuildContext context) {
    final items = [
      _CrudItem('Data mobil', Icons.directions_car_outlined),
      _CrudItem('Kategori', Icons.category_outlined),
      _CrudItem('Pelanggan', Icons.people_outline),
      _CrudItem('Booking', Icons.event_note_outlined),
    ];

    return GridView.count(
      crossAxisCount: 2,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      mainAxisSpacing: 12,
      crossAxisSpacing: 12,
      childAspectRatio: 1.6,
      children: items.map((item) => _crudCard(context, item)).toList(),
    );
  }

  Widget _crudCard(BuildContext context, _CrudItem item) {
    return InkWell(
      borderRadius: BorderRadius.circular(12),
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => _PlaceholderScreen(title: item.label),
          ),
        );
      },
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: const Color(0xFF1A1A1A),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: Colors.white12),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Icon(item.icon, color: Colors.amber, size: 22),
            Text(
              item.label,
              style: const TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: Colors.white,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildOtherMenu(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: const Color(0xFF1A1A1A),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.white12),
      ),
      child: Column(
        children: [
          _menuTile(
            context,
            icon: Icons.bar_chart_outlined,
            label: 'Laporan dan statistik',
            showDivider: true,
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => const _PlaceholderScreen(title: 'Laporan dan statistik'),
              ),
            ),
          ),
          _menuTile(
            context,
            icon: Icons.settings_outlined,
            label: 'Pengaturan',
            showDivider: true,
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => const _PlaceholderScreen(title: 'Pengaturan'),
              ),
            ),
          ),
          _menuTile(
            context,
            icon: Icons.logout,
            label: 'Keluar',
            iconColor: Colors.redAccent,
            labelColor: Colors.redAccent,
            showDivider: false,
            onTap: () {
              // TODO: implementasi logout (hapus token, redirect ke login)
            },
          ),
        ],
      ),
    );
  }

  Widget _menuTile(
    BuildContext context, {
    required IconData icon,
    required String label,
    required bool showDivider,
    required VoidCallback onTap,
    Color iconColor = Colors.white60,
    Color labelColor = Colors.white,
  }) {
    return InkWell(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        decoration: BoxDecoration(
          border: showDivider
              ? const Border(bottom: BorderSide(color: Colors.white12, width: 0.5))
              : null,
        ),
        child: Row(
          children: [
            Icon(icon, size: 18, color: iconColor),
            const SizedBox(width: 12),
            Expanded(
              child: Text(label, style: TextStyle(fontSize: 14, color: labelColor)),
            ),
            if (labelColor != Colors.redAccent)
              const Icon(Icons.chevron_right, size: 16, color: Colors.white38),
          ],
        ),
      ),
    );
  }
}

class _CrudItem {
  final String label;
  final IconData icon;
  _CrudItem(this.label, this.icon);
}

/// Halaman CRUD untuk tiap menu (Data Mobil, Kategori, Pelanggan, Booking).
/// UI sudah lengkap (list, tombol tambah, ikon edit/hapus) tapi semua aksi
/// masih dummy/placeholder — belum terhubung ke API atau logic apapun.
class _PlaceholderScreen extends StatelessWidget {
  final String title;
  const _PlaceholderScreen({required this.title});

  // Data dummy hanya untuk menampilkan bentuk list-nya.
  static const List<String> _dummyItems = ['Item contoh 1', 'Item contoh 2', 'Item contoh 3'];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0E0E0E),
      appBar: AppBar(
        backgroundColor: const Color(0xFF0E0E0E),
        title: Text(title, style: const TextStyle(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      floatingActionButton: FloatingActionButton(
        backgroundColor: Colors.amber,
        onPressed: () {
          // TODO: buka form tambah data, belum difungsikan
        },
        child: const Icon(Icons.add, color: Colors.black),
      ),
      body: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: _dummyItems.length,
        separatorBuilder: (_, __) => const SizedBox(height: 10),
        itemBuilder: (context, index) {
          return Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: const Color(0xFF1A1A1A),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: Colors.white12),
            ),
            child: Row(
              children: [
                Expanded(
                  child: Text(
                    _dummyItems[index],
                    style: const TextStyle(fontSize: 14, color: Colors.white),
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.edit_outlined, size: 18, color: Colors.white60),
                  onPressed: () {
                    // TODO: buka form edit data, belum difungsikan
                  },
                ),
                IconButton(
                  icon: const Icon(Icons.delete_outline, size: 18, color: Colors.redAccent),
                  onPressed: () {
                    // TODO: hapus data, belum difungsikan
                  },
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}

/// Halaman profil admin — nama dan email dinamis sesuai user yang login.
/// Belum ada logic edit profil, masih tampilan saja.
class AdminProfileScreen extends StatelessWidget {
  final String adminName;
  final String adminEmail;

  const AdminProfileScreen({
    super.key,
    required this.adminName,
    required this.adminEmail,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0E0E0E),
      appBar: AppBar(
        backgroundColor: const Color(0xFF0E0E0E),
        title: const Text('Profil admin', style: TextStyle(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            CircleAvatar(
              radius: 32,
              backgroundColor: Colors.amber.withOpacity(0.2),
              child: Text(
                adminName.isNotEmpty ? adminName[0].toUpperCase() : '?',
                style: const TextStyle(
                  color: Colors.amber,
                  fontSize: 22,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),
            const SizedBox(height: 16),
            Text(
              adminName,
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: Colors.white,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              adminEmail,
              style: const TextStyle(fontSize: 14, color: Colors.white60),
            ),
            // TODO: tambahkan form edit profil, ganti password, dll.
          ],
        ),
      ),
    );
  }
}