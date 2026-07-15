import 'package:flutter/material.dart';
import '../../services/mobil_service.dart';
import '../../services/api_service.dart';

class DataMobilScreen extends StatefulWidget {
  const DataMobilScreen({super.key});

  @override
  State<DataMobilScreen> createState() => _DataMobilScreenState();
}

class _DataMobilScreenState extends State<DataMobilScreen> {
  static const Color primaryYellow = Color(0xFFFBBF24);
  static const Color darkNavy = Color(0xFF0F172A);

  bool loading = true;
  List<Map<String, dynamic>> mobilList = [];
  List<Map<String, dynamic>> kategoriList = [];

  @override
  void initState() {
    super.initState();
    fetchMobil();
    fetchKategori();
  }

  Future<void> fetchMobil() async {
    setState(() => loading = true);

    final data = await MobilService.getMobil();

    setState(() {
      mobilList = List<Map<String, dynamic>>.from(data);
      loading = false;
    });
  }

  Future<void> fetchKategori() async {
    final result = await ApiService.getKategori();
    if (result['success'] == true && result['data'] != null) {
      setState(() {
        kategoriList = List<Map<String, dynamic>>.from(result['data']);
      });
    }
  }

  Future<void> deleteMobil(int id) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Mobil'),
        content: const Text('Apakah kamu yakin ingin menghapus data mobil ini?'),
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

    final result = await MobilService.deleteMobil(id);

    if (!mounted) return;

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Mobil berhasil dihapus')),
      );
      fetchMobil();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? 'Gagal menghapus mobil')),
      );
    }
  }

  void openFormDialog({Map<String, dynamic>? mobil}) {
    final isEdit = mobil != null;
    final namaController = TextEditingController(text: mobil?['nama_mobil']?.toString() ?? '');
    final merkController = TextEditingController(text: mobil?['merk']?.toString() ?? '');
    final platController = TextEditingController(text: mobil?['plat_nomor']?.toString() ?? '');
    final hargaController = TextEditingController(text: mobil?['harga_sewa_per_hari']?.toString() ?? '');
    final rawKategoriId = mobil?['kategori_id'];
    int? selectedKategoriId = rawKategoriId is int
    ? rawKategoriId
    : int.tryParse(rawKategoriId?.toString() ?? '');
    String selectedStatus = mobil?['status']?.toString() ?? 'tersedia';

    showDialog(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setDialogState) => AlertDialog(
          title: Text(isEdit ? 'Edit Mobil' : 'Tambah Mobil'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(
                  controller: namaController,
                  decoration: const InputDecoration(labelText: 'Nama Mobil'),
                ),
                TextField(
                  controller: merkController,
                  decoration: const InputDecoration(labelText: 'Merk'),
                ),
                TextField(
                  controller: platController,
                  decoration: const InputDecoration(labelText: 'Plat Nomor'),
                ),
                TextField(
                  controller: hargaController,
                  decoration: const InputDecoration(labelText: 'Harga Sewa / Hari'),
                  keyboardType: TextInputType.number,
                ),
                const SizedBox(height: 12),
                DropdownButtonFormField<int>(
                  initialValue: selectedKategoriId,
                  decoration: const InputDecoration(labelText: 'Kategori'),
                  items: kategoriList
                      .map((k) => DropdownMenuItem<int>(
                            value: k['id'] is int ? k['id'] : int.tryParse(k['id'].toString()),
                            // Field nama kategori di database bernama 'nama_kategori',
                            // bukan 'nama'. Sebelumnya pakai k['nama'] sehingga selalu
                            // null dan dropdown tampak kosong.
                            child: Text(k['nama_kategori']?.toString() ?? '-'),
                          ))
                      .toList(),
                  onChanged: (value) {
                    setDialogState(() => selectedKategoriId = value);
                  },
                ),
                const SizedBox(height: 12),
                DropdownButtonFormField<String>(
                  initialValue: selectedStatus,
                  decoration: const InputDecoration(labelText: 'Status'),
                  items: const [
                    DropdownMenuItem(value: 'tersedia', child: Text('Tersedia')),
                    DropdownMenuItem(value: 'servis', child: Text('Servis')),
                    DropdownMenuItem(value: 'disewa', child: Text('Disewa')),
                  ],
                  onChanged: (value) {
                    if (value != null) {
                      setDialogState(() => selectedStatus = value);
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
                if (selectedKategoriId == null) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('Pilih kategori dulu')),
                  );
                  return;
                }

                final data = {
                  'nama_mobil': namaController.text.trim(),
                  'merk': merkController.text.trim(),
                  'plat_nomor': platController.text.trim(),
                  'harga_sewa_per_hari': hargaController.text.trim(),
                  'kategori_id': selectedKategoriId,
                  'status': selectedStatus,
                };

                Map<String, dynamic> result;
                if (isEdit) {
                  result = await MobilService.updateMobil(mobil['id'], data);
                } else {
                  result = await MobilService.addMobil(data);
                }

                if (!mounted) return;
                Navigator.pop(context);

                if (result['success'] == true) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text(isEdit ? 'Mobil berhasil diupdate' : 'Mobil berhasil ditambahkan')),
                  );
                  fetchMobil();
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

  Color _statusColor(String status) {
    switch (status.toLowerCase()) {
      case 'tersedia':
        return Colors.green;
      case 'servis':
        return Colors.orange;
      case 'disewa':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF4F5F7),
      appBar: AppBar(
        backgroundColor: darkNavy,
        title: const Text('Data Mobil', style: TextStyle(color: Colors.white)),
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
              onRefresh: fetchMobil,
              child: mobilList.isEmpty
                  ? ListView(
                      children: const [
                        SizedBox(height: 120),
                        Center(child: Text('Belum ada data mobil')),
                      ],
                    )
                  : ListView.builder(
                      padding: const EdgeInsets.all(16),
                      itemCount: mobilList.length,
                      itemBuilder: (context, index) {
                        final mobil = mobilList[index];
                        final status = mobil['status']?.toString() ?? '-';

                        return Card(
                          margin: const EdgeInsets.only(bottom: 12),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                          child: ListTile(
                            contentPadding: const EdgeInsets.all(12),
                            leading: Container(
                              width: 44,
                              height: 44,
                              decoration: BoxDecoration(
                                color: const Color(0xFFFDF3D7),
                                borderRadius: BorderRadius.circular(10),
                              ),
                              child: const Icon(Icons.directions_car_outlined, color: primaryYellow),
                            ),
                            title: Text(
                              mobil['nama_mobil']?.toString() ?? '-',
                              style: const TextStyle(fontWeight: FontWeight.bold),
                            ),
                            subtitle: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('${mobil['merk'] ?? '-'} • ${mobil['plat_nomor'] ?? '-'}'),
                                Text('Rp ${mobil['harga_sewa_per_hari'] ?? 0}/hari'),
                                Container(
                                  margin: const EdgeInsets.only(top: 4),
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                  decoration: BoxDecoration(
                                    color: _statusColor(status).withValues(alpha: 0.12),
                                    borderRadius: BorderRadius.circular(6),
                                  ),
                                  child: Text(
                                    status,
                                    style: TextStyle(
                                      color: _statusColor(status),
                                      fontSize: 11,
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            isThreeLine: true,
                            trailing: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                IconButton(
                                  icon: const Icon(Icons.edit_outlined, color: Colors.blue),
                                  onPressed: () => openFormDialog(mobil: mobil),
                                ),
                                IconButton(
                                  icon: const Icon(Icons.delete_outline, color: Colors.red),
                                  onPressed: () => deleteMobil(mobil['id']),
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