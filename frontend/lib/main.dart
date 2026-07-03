import 'package:flutter/material.dart';
import 'services/api_service.dart';

void main() {
  runApp(const RentWheelApp());
}

class RentWheelApp extends StatelessWidget {
  const RentWheelApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'RentWheel',
      theme: ThemeData(primarySwatch: Colors.indigo),
      home: const MobilListPage(),
    );
  }
}

class MobilListPage extends StatefulWidget {
  const MobilListPage({super.key});

  @override
  State<MobilListPage> createState() => _MobilListPageState();
}

class _MobilListPageState extends State<MobilListPage> {
  final ApiService _api = ApiService();
  List<dynamic> _mobils = [];
  bool _loading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadMobils();
  }

  Future<void> _loadMobils() async {
    try {
      final data = await _api.getMobils();
      setState(() {
        _mobils = data;
        _loading = false;
      });
    } catch (e) {
      setState(() {
        _error = 'Gagal memuat data. Pastikan backend Laravel sedang berjalan.';
        _loading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('RentWheel — Daftar Mobil')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _error != null
              ? Center(child: Text(_error!))
              : ListView.builder(
                  itemCount: _mobils.length,
                  itemBuilder: (context, index) {
                    final mobil = _mobils[index];
                    return ListTile(
                      title: Text(mobil['nama_mobil'] ?? '-'),
                      subtitle: Text('Rp ${mobil['harga_sewa_per_hari']} / hari'),
                      trailing: Text(mobil['status'] ?? '-'),
                    );
                  },
                ),
    );
  }
}
