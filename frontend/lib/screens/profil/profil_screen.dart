import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import 'riwayat_booking_screen.dart';

class ProfilScreen extends StatefulWidget {
  const ProfilScreen({super.key});

  @override
  State<ProfilScreen> createState() => _ProfilScreenState();
}

class _ProfilScreenState extends State<ProfilScreen> {
  // ==================== PALET WARNA (sama dengan layar lain) ====================
  static const Color navyDark = Color(0xFF14213D);
  static const Color navyLight = Color(0xFF223159);
  static const Color amber = Color(0xFFFFB703);
  static const Color orange = Color(0xFFFB8500);
  static const Color teal = Color(0xFF2EC4B6);
  static const Color bgTop = Color(0xFFFFF6EA);
  static const Color bgBottom = Color(0xFFE9F3F4);

  Map<String, dynamic>? user;
  bool loading = true;

  @override
  void initState() {
    super.initState();
    _loadUser();
  }

  Future<void> _loadUser() async {
    // Ambil dari local storage dulu (instan, tidak perlu tunggu API).
    final saved = await ApiService.getSavedUser();
    if (mounted) {
      setState(() {
        user = saved;
        loading = false;
      });
    }

    // Sinkronkan sekalian dengan server (opsional, kalau data user berubah).
    final result = await ApiService.me();
    if (mounted && result["success"] == true && result["user"] != null) {
      setState(() => user = Map<String, dynamic>.from(result["user"]));
    }
  }

  Future<void> _confirmLogout() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
        title: const Text('Keluar akun?', style: TextStyle(color: navyDark, fontWeight: FontWeight.bold)),
        content: const Text('Anda perlu login kembali untuk mengakses akun ini.'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: Text('Batal', style: TextStyle(color: Colors.grey.shade600)),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Keluar', style: TextStyle(color: Color(0xFFEF476F), fontWeight: FontWeight.w700)),
          ),
        ],
      ),
    );

    if (confirmed == true) {
      await ApiService.logout();
      if (!mounted) return;
      Navigator.pushNamedAndRemoveUntil(context, '/login', (route) => false);
    }
  }

  String _initials(String? name) {
    if (name == null || name.trim().isEmpty) return '?';
    final parts = name.trim().split(RegExp(r'\s+'));
    if (parts.length == 1) return parts[0][0].toUpperCase();
    return (parts[0][0] + parts[1][0]).toUpperCase();
  }

  InputDecoration _dialogFieldDecoration(String label, {Widget? suffixIcon}) {
    return InputDecoration(
      labelText: label,
      suffixIcon: suffixIcon,
      filled: true,
      fillColor: const Color(0xFFF6F6F6),
      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
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
    );
  }

  ButtonStyle get _dialogSaveButtonStyle => ElevatedButton.styleFrom(
        backgroundColor: orange,
        foregroundColor: Colors.white,
        elevation: 0,
        padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 12),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      );

  void _openEditProfileDialog() {
    final namaController = TextEditingController(text: user?['name']?.toString() ?? '');
    final emailController = TextEditingController(text: user?['email']?.toString() ?? '');
    final hpController = TextEditingController(text: user?['no_hp']?.toString() ?? '');
    final ktpController = TextEditingController(text: user?['no_ktp']?.toString() ?? '');
    final alamatController = TextEditingController(text: user?['alamat']?.toString() ?? '');
    bool saving = false;

    showDialog(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setDialogState) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
          title: const Text('Edit Profil', style: TextStyle(color: navyDark, fontWeight: FontWeight.bold)),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(controller: namaController, decoration: _dialogFieldDecoration('Nama')),
                const SizedBox(height: 12),
                TextField(
                  controller: emailController,
                  decoration: _dialogFieldDecoration('Email'),
                  keyboardType: TextInputType.emailAddress,
                ),
                const SizedBox(height: 12),
                TextField(
                  controller: hpController,
                  decoration: _dialogFieldDecoration('No. HP'),
                  keyboardType: TextInputType.phone,
                ),
                const SizedBox(height: 12),
                TextField(controller: ktpController, decoration: _dialogFieldDecoration('No. KTP')),
                const SizedBox(height: 12),
                TextField(
                  controller: alamatController,
                  decoration: _dialogFieldDecoration('Alamat'),
                  maxLines: 2,
                ),
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: saving ? null : () => Navigator.pop(context),
              child: Text('Batal', style: TextStyle(color: Colors.grey.shade600)),
            ),
            ElevatedButton(
              style: _dialogSaveButtonStyle,
              onPressed: saving
                  ? null
                  : () async {
                      if (namaController.text.trim().isEmpty || emailController.text.trim().isEmpty) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('Nama dan email wajib diisi')),
                        );
                        return;
                      }

                      setDialogState(() => saving = true);

                      final result = await ApiService.updateProfile({
                        'name': namaController.text.trim(),
                        'email': emailController.text.trim(),
                        'no_hp': hpController.text.trim(),
                        'no_ktp': ktpController.text.trim(),
                        'alamat': alamatController.text.trim(),
                      });

                      if (!mounted) return;
                      Navigator.pop(context);

                      if (result['success'] == true) {
                        setState(() {
                          if (result['user'] != null) {
                            user = Map<String, dynamic>.from(result['user']);
                          }
                        });
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('Profil berhasil diupdate')),
                        );
                      } else {
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(content: Text(result['message'] ?? 'Gagal mengupdate profil')),
                        );
                      }
                    },
              child: saving
                  ? const SizedBox(
                      width: 18,
                      height: 18,
                      child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                    )
                  : const Text('Simpan'),
            ),
          ],
        ),
      ),
    );
  }

  void _openChangePasswordDialog() {
    final currentController = TextEditingController();
    final newController = TextEditingController();
    final confirmController = TextEditingController();
    bool saving = false;
    bool obscureCurrent = true;
    bool obscureNew = true;
    bool obscureConfirm = true;

    showDialog(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setDialogState) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
          title: const Text('Ganti Password', style: TextStyle(color: navyDark, fontWeight: FontWeight.bold)),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(
                  controller: currentController,
                  obscureText: obscureCurrent,
                  decoration: _dialogFieldDecoration(
                    'Password Lama',
                    suffixIcon: IconButton(
                      icon: Icon(obscureCurrent ? Icons.visibility_off : Icons.visibility,
                          color: Colors.grey.shade500, size: 20),
                      onPressed: () => setDialogState(() => obscureCurrent = !obscureCurrent),
                    ),
                  ),
                ),
                const SizedBox(height: 12),
                TextField(
                  controller: newController,
                  obscureText: obscureNew,
                  decoration: _dialogFieldDecoration(
                    'Password Baru (min. 8 karakter)',
                    suffixIcon: IconButton(
                      icon: Icon(obscureNew ? Icons.visibility_off : Icons.visibility,
                          color: Colors.grey.shade500, size: 20),
                      onPressed: () => setDialogState(() => obscureNew = !obscureNew),
                    ),
                  ),
                ),
                const SizedBox(height: 12),
                TextField(
                  controller: confirmController,
                  obscureText: obscureConfirm,
                  decoration: _dialogFieldDecoration(
                    'Konfirmasi Password Baru',
                    suffixIcon: IconButton(
                      icon: Icon(obscureConfirm ? Icons.visibility_off : Icons.visibility,
                          color: Colors.grey.shade500, size: 20),
                      onPressed: () => setDialogState(() => obscureConfirm = !obscureConfirm),
                    ),
                  ),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: saving ? null : () => Navigator.pop(context),
              child: Text('Batal', style: TextStyle(color: Colors.grey.shade600)),
            ),
            ElevatedButton(
              style: _dialogSaveButtonStyle,
              onPressed: saving
                  ? null
                  : () async {
                      if (currentController.text.isEmpty ||
                          newController.text.isEmpty ||
                          confirmController.text.isEmpty) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('Semua kolom wajib diisi')),
                        );
                        return;
                      }
                      if (newController.text.length < 8) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('Password baru minimal 8 karakter')),
                        );
                        return;
                      }
                      if (newController.text != confirmController.text) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('Konfirmasi password tidak cocok')),
                        );
                        return;
                      }

                      setDialogState(() => saving = true);

                      final result = await ApiService.changePassword(
                        currentPassword: currentController.text,
                        newPassword: newController.text,
                        newPasswordConfirmation: confirmController.text,
                      );

                      if (!mounted) return;
                      Navigator.pop(context);

                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text(
                            result['success'] == true
                                ? (result['message'] ?? 'Password berhasil diubah')
                                : (result['message'] ?? 'Gagal mengubah password'),
                          ),
                        ),
                      );
                    },
              child: saving
                  ? const SizedBox(
                      width: 18,
                      height: 18,
                      child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                    )
                  : const Text('Simpan'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _menuTile({
    required IconData icon,
    required Color color,
    required String title,
    required VoidCallback onTap,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
          child: Row(
            children: [
              Container(
                width: 38,
                height: 38,
                decoration: BoxDecoration(
                  color: color.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(11),
                ),
                child: Icon(icon, size: 18, color: color),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Text(title, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13.5, color: navyDark)),
              ),
              Icon(Icons.chevron_right_rounded, size: 20, color: Colors.grey.shade400),
            ],
          ),
        ),
      ),
    );
  }

  Widget _menuGroup(List<Widget> tiles) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: navyDark.withValues(alpha: 0.05),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        children: [
          for (int i = 0; i < tiles.length; i++) ...[
            tiles[i],
            if (i != tiles.length - 1) Divider(height: 1, indent: 14, endIndent: 14, color: Colors.grey.shade100),
          ],
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final name = user?['name']?.toString() ?? 'Pengguna';
    final email = user?['email']?.toString() ?? '-';
    final role = user?['role']?.toString() ?? 'user';
    final noHp = user?['no_hp']?.toString();
    final alamat = user?['alamat']?.toString();

    return Scaffold(
      backgroundColor: bgTop,
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [bgTop, bgBottom],
          ),
        ),
        child: SafeArea(
          child: loading
              ? const Center(child: CircularProgressIndicator(color: orange))
              : ListView(
                  padding: const EdgeInsets.fromLTRB(20, 20, 20, 24),
                  children: [
                    // ---------- Hero card profil ----------
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.fromLTRB(20, 26, 20, 22),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                          colors: [navyDark, navyLight],
                        ),
                        borderRadius: BorderRadius.circular(22),
                      ),
                      child: Column(
                        children: [
                          Container(
                            width: 78,
                            height: 78,
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(colors: [amber, orange]),
                              shape: BoxShape.circle,
                              border: Border.all(color: Colors.white.withValues(alpha: 0.2), width: 3),
                            ),
                            child: Center(
                              child: Text(
                                _initials(name),
                                style: const TextStyle(color: Colors.white, fontSize: 26, fontWeight: FontWeight.bold),
                              ),
                            ),
                          ),
                          const SizedBox(height: 14),
                          Text(
                            name,
                            style: const TextStyle(fontSize: 17, fontWeight: FontWeight.bold, color: Colors.white),
                          ),
                          const SizedBox(height: 4),
                          Text(email, style: const TextStyle(fontSize: 12.5, color: Colors.white70)),
                          if (noHp != null && noHp.isNotEmpty) ...[
                            const SizedBox(height: 2),
                            Text(noHp, style: const TextStyle(fontSize: 12.5, color: Colors.white70)),
                          ],
                          if (alamat != null && alamat.isNotEmpty) ...[
                            const SizedBox(height: 2),
                            Text(
                              alamat,
                              textAlign: TextAlign.center,
                              style: const TextStyle(fontSize: 12.5, color: Colors.white70),
                            ),
                          ],
                          const SizedBox(height: 10),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                            decoration: BoxDecoration(
                              color: amber.withValues(alpha: 0.16),
                              borderRadius: BorderRadius.circular(20),
                              border: Border.all(color: amber.withValues(alpha: 0.4)),
                            ),
                            child: Text(
                              role.toUpperCase(),
                              style: const TextStyle(fontSize: 10.5, fontWeight: FontWeight.w800, color: amber),
                            ),
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 22),

                    Padding(
                      padding: const EdgeInsets.only(left: 4, bottom: 8),
                      child: Text('Akun',
                          style: TextStyle(fontSize: 12.5, fontWeight: FontWeight.w700, color: Colors.grey.shade500)),
                    ),
                    _menuGroup([
                      _menuTile(
                        icon: Icons.edit_rounded,
                        color: teal,
                        title: 'Edit Profil',
                        onTap: _openEditProfileDialog,
                      ),
                      _menuTile(
                        icon: Icons.lock_rounded,
                        color: orange,
                        title: 'Ganti Password',
                        onTap: _openChangePasswordDialog,
                      ),
                      _menuTile(
                        icon: Icons.receipt_long_rounded,
                        color: amber,
                        title: 'Riwayat Booking',
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(builder: (context) => const RiwayatBookingScreen()),
                          );
                        },
                      ),
                    ]),

                    const SizedBox(height: 20),

                    Padding(
                      padding: const EdgeInsets.only(left: 4, bottom: 8),
                      child: Text('Lainnya',
                          style: TextStyle(fontSize: 12.5, fontWeight: FontWeight.w700, color: Colors.grey.shade500)),
                    ),
                    _menuGroup([
                      _menuTile(
                        icon: Icons.logout_rounded,
                        color: const Color(0xFFEF476F),
                        title: 'Keluar',
                        onTap: _confirmLogout,
                      ),
                    ]),
                  ],
                ),
        ),
      ),
    );
  }
}