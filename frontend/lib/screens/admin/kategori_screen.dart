import 'package:flutter/material.dart';
import '../../services/api_service.dart';

// Field kategori di database (tabel kategori_mobil) cuma ada:
// id, nama_kategori, created_at, updated_at.
// Sebelumnya file ini pakai 'nama' & 'deskripsi' yang tidak match
// dengan field asli backend, sehingga data selalu tampil kosong ('-').
// Sudah diperbaiki: pakai 'nama_kategori', dan field 'deskripsi' dihapus
// karena memang tidak ada kolomnya di database.

class KategoriScreen extends StatefulWidget {
  const KategoriScreen({super.key});

  @override
  State<KategoriScreen> createState() => _KategoriScreenState();
}

class _KategoriScreenState extends State<KategoriScreen> {
  static const Color primaryYellow = Color(0xFFFBBF24);
  static const Color darkNavy = Color(0xFF0F172A);

  bool loading = true;
  List<Map<String, dynamic>> kategoriList = [];

  @override
  void initState() {
    super.initState();
    fetchKategori();
  }

  Future<void> fetchKategori() async {
    setState(() => loading = true);

    final result = await ApiService.getKategori();

    setState(() {
      if (result['success'] == true && result['data'] != null) {
        kategoriList = List<Map<String, dynamic>>.from(result['data']);
      }
      loading = false;
    });
  }

  Future<void> deleteKategori(int id) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Kategori'),
        content: const Text('Apakah kamu yakin ingin menghapus kategori ini?'),
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

    final result = await ApiService.deleteKategori(id);

    if (!mounted) return;

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Kategori berhasil dihapus')),
      );
      fetchKategori();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? 'Gagal menghapus kategori')),
      );
    }
  }

  void openFormDialog({Map<String, dynamic>? kategori}) {
    final isEdit = kategori != null;
    final namaController =
        TextEditingController(text: kategori?['nama_kategori']?.toString() ?? '');

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text(isEdit ? 'Edit Kategori' : 'Tambah Kategori'),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              TextField(
                controller: namaController,
                decoration: const InputDecoration(labelText: 'Nama Kategori'),
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
              if (namaController.text.trim().isEmpty) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Nama kategori tidak boleh kosong')),
                );
                return;
              }

              final data = {
                'nama_kategori': namaController.text.trim(),
              };

              Map<String, dynamic> result;
              if (isEdit) {
                result = await ApiService.updateKategori(kategori['id'], data);
              } else {
                result = await ApiService.addKategori(data);
              }

              if (!mounted) return;
              Navigator.pop(context);

              if (result['success'] == true) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                      content: Text(isEdit
                          ? 'Kategori berhasil diupdate'
                          : 'Kategori berhasil ditambahkan')),
                );
                fetchKategori();
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
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF4F5F7),
      appBar: AppBar(
        backgroundColor: darkNavy,
        title: const Text('Kategori', style: TextStyle(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      floatingActionButton: FloatingActionButton(
        backgroundColor: primaryYellow,
        onPressed: () => openFormDialog(),
        child: const Icon(Icons.add, color: Colors.black87),
      ),
      body: loading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: fetchKategori,
              child: kategoriList.isEmpty
                  ? ListView(
                      children: const [
                        SizedBox(height: 120),
                        Center(child: Text('Belum ada data kategori')),
                      ],
                    )
                  : ListView.builder(
                      padding: const EdgeInsets.all(16),
                      itemCount: kategoriList.length,
                      itemBuilder: (context, index) {
                        final kategori = kategoriList[index];
                        return Card(
                          margin: const EdgeInsets.only(bottom: 12),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                          child: ListTile(
                            contentPadding: const EdgeInsets.all(12),
                            leading: Container(
                              width: 44,
                              height: 44,
                              decoration: BoxDecoration(
                                color: const Color(0xFFEFE5FB),
                                borderRadius: BorderRadius.circular(10),
                              ),
                              child: const Icon(Icons.category_outlined, color: Colors.purple),
                            ),
                            title: Text(
                              kategori['nama_kategori']?.toString() ?? '-',
                              style: const TextStyle(fontWeight: FontWeight.bold),
                            ),
                            trailing: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                IconButton(
                                  icon: const Icon(Icons.edit_outlined, color: Colors.blue),
                                  onPressed: () => openFormDialog(kategori: kategori),
                                ),
                                IconButton(
                                  icon: const Icon(Icons.delete_outline, color: Colors.red),
                                  onPressed: () => deleteKategori(kategori['id']),
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