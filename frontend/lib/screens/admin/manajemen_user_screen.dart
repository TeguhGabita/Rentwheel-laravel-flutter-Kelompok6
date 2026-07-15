import 'package:flutter/material.dart';
import '../../services/api_service.dart';

class ManajemenUserScreen extends StatefulWidget {
  const ManajemenUserScreen({super.key});

  @override
  State<ManajemenUserScreen> createState() => _ManajemenUserScreenState();
}

class _ManajemenUserScreenState extends State<ManajemenUserScreen> {
  static const Color primaryYellow = Color(0xFFFBBF24);
  static const Color darkNavy = Color(0xFF0F172A);

  bool loading = true;
  List<Map<String, dynamic>> userList = [];

  @override
  void initState() {
    super.initState();
    fetchUser();
  }

  Future<void> fetchUser() async {
    setState(() => loading = true);

    final result = await ApiService.getUser();

    setState(() {
      if (result['success'] == true && result['data'] != null) {
        userList = List<Map<String, dynamic>>.from(result['data']);
      }
      loading = false;
    });
  }

  Future<void> deleteUser(int id) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus User'),
        content: const Text('Apakah kamu yakin ingin menghapus user ini?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Hapus', style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    final result = await ApiService.deleteUser(id);

    if (!mounted) return;

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('User berhasil dihapus')),
      );
      fetchUser();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? 'Gagal menghapus user')),
      );
    }
  }

  void openFormDialog({Map<String, dynamic>? user}) {
    final isEdit = user != null;
    final nameController = TextEditingController(text: user?['name']?.toString() ?? '');
    final emailController = TextEditingController(text: user?['email']?.toString() ?? '');
    final passwordController = TextEditingController();
    final noKtpController = TextEditingController(text: user?['no_ktp']?.toString() ?? '');
    final noHpController = TextEditingController(text: user?['no_hp']?.toString() ?? '');
    final alamatController = TextEditingController(text: user?['alamat']?.toString() ?? '');
    String selectedRole = user?['role']?.toString() ?? 'user';

    showDialog(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setDialogState) => AlertDialog(
          title: Text(isEdit ? 'Edit User' : 'Tambah User'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(
                  controller: nameController,
                  decoration: const InputDecoration(labelText: 'Nama'),
                ),
                TextField(
                  controller: emailController,
                  decoration: const InputDecoration(labelText: 'Email'),
                  keyboardType: TextInputType.emailAddress,
                ),
                TextField(
                  controller: passwordController,
                  decoration: InputDecoration(
                    labelText: isEdit ? 'Password (kosongkan jika tidak diubah)' : 'Password',
                  ),
                  obscureText: true,
                ),
                TextField(
                  controller: noKtpController,
                  decoration: const InputDecoration(labelText: 'No. KTP'),
                  keyboardType: TextInputType.number,
                ),
                TextField(
                  controller: noHpController,
                  decoration: const InputDecoration(labelText: 'No. HP'),
                  keyboardType: TextInputType.phone,
                ),
                TextField(
                  controller: alamatController,
                  decoration: const InputDecoration(labelText: 'Alamat'),
                  maxLines: 2,
                ),
                const SizedBox(height: 12),
                DropdownButtonFormField<String>(
                  initialValue: selectedRole,
                  decoration: const InputDecoration(labelText: 'Role'),
                  items: const [
                    DropdownMenuItem(value: 'user', child: Text('User / Pelanggan')),
                    DropdownMenuItem(value: 'admin', child: Text('Admin')),
                  ],
                  onChanged: (value) {
                    if (value != null) {
                      setDialogState(() => selectedRole = value);
                    }
                  },
                ),
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('Batal'),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: primaryYellow),
              onPressed: () async {
                final data = {
                  'name': nameController.text.trim(),
                  'email': emailController.text.trim(),
                  'no_ktp': noKtpController.text.trim(),
                  'no_hp': noHpController.text.trim(),
                  'alamat': alamatController.text.trim(),
                  'role': selectedRole,
                };

                // Password cuma dikirim kalau diisi (khusus edit; wajib diisi kalau tambah baru)
                if (passwordController.text.isNotEmpty) {
                  data['password'] = passwordController.text;
                }

                Map<String, dynamic> result;
                if (isEdit) {
                  result = await ApiService.updateUser(user['id'], data);
                } else {
                  result = await ApiService.addUser(data);
                }

                if (!mounted) return;
                Navigator.pop(context);

                if (result['success'] == true) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text(isEdit ? 'User berhasil diupdate' : 'User berhasil ditambahkan')),
                  );
                  fetchUser();
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text(result['message'] ?? 'Terjadi kesalahan')),
                  );
                }
              },
              child: const Text('Simpan'),
            ),
          ],
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
        title: const Text('Manajemen User', style: TextStyle(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      floatingActionButton: FloatingActionButton(
        backgroundColor: primaryYellow,
        onPressed: () => openFormDialog(),
        child: const Icon(Icons.person_add_alt_1_outlined, color: Colors.black87),
      ),
      body: loading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: fetchUser,
              child: userList.isEmpty
                  ? ListView(
                      children: const [
                        SizedBox(height: 120),
                        Center(child: Text('Belum ada data user')),
                      ],
                    )
                  : ListView.builder(
                      padding: const EdgeInsets.all(16),
                      itemCount: userList.length,
                      itemBuilder: (context, index) {
                        final user = userList[index];
                        final role = user['role']?.toString() ?? 'user';
                        final isAdmin = role.toLowerCase() == 'admin';

                        return Card(
                          margin: const EdgeInsets.only(bottom: 12),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                          child: ListTile(
                            contentPadding: const EdgeInsets.all(12),
                            leading: CircleAvatar(
                              backgroundColor: isAdmin ? const Color(0xFFFDF3D7) : const Color(0xFFE0EEFD),
                              child: Text(
                                (user['name']?.toString().isNotEmpty == true)
                                    ? user['name'].toString()[0].toUpperCase()
                                    : '?',
                                style: TextStyle(
                                  color: isAdmin ? primaryYellow : Colors.blue,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                            title: Text(
                              user['name']?.toString() ?? '-',
                              style: const TextStyle(fontWeight: FontWeight.bold),
                            ),
                            subtitle: Text(
                              '${user['email'] ?? '-'} • ${user['no_hp'] ?? '-'}',
                            ),
                            trailing: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                  decoration: BoxDecoration(
                                    color: (isAdmin ? primaryYellow : Colors.blue).withValues(alpha: 0.12),
                                    borderRadius: BorderRadius.circular(6),
                                  ),
                                  child: Text(
                                    isAdmin ? 'Admin' : 'User',
                                    style: TextStyle(
                                      color: isAdmin ? Colors.orange.shade800 : Colors.blue,
                                      fontSize: 11,
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                ),
                                IconButton(
                                  icon: const Icon(Icons.edit_outlined, color: Colors.blue),
                                  onPressed: () => openFormDialog(user: user),
                                ),
                                IconButton(
                                  icon: const Icon(Icons.delete_outline, color: Colors.red),
                                  onPressed: () => deleteUser(user['id']),
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
            ),
    );
  }
}