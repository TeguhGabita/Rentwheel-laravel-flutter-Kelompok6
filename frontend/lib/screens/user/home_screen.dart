import 'package:flutter/material.dart';

/// Halaman utama untuk user (bukan admin) — menampilkan daftar mobil
/// yang tersedia untuk disewa. Tampilan dirapikan untuk layar mobile:
/// grid 2 kolom, kartu konsisten, spacing rapi, responsif ke lebar layar.
class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  // Data dummy — nanti diganti hasil fetch dari GET /api/mobils
  static const List<_CarItem> _cars = [
    _CarItem(name: 'Brio', category: 'City car'),
    _CarItem(name: 'Avanza', category: 'MPV'),
    _CarItem(name: 'Xenia', category: 'MPV'),
    _CarItem(name: 'Fortuner', category: 'SUV'),
    _CarItem(name: 'Pajero sport', category: 'SUV'),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0E0E0E),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildHeader(context),
              const SizedBox(height: 20),
              Expanded(child: _buildCarGrid()),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHeader(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        const Text(
          'RentWheel',
          style: TextStyle(
            fontSize: 22,
            fontWeight: FontWeight.w600,
            color: Colors.white,
          ),
        ),
        IconButton(
          icon: const Icon(Icons.logout, color: Colors.white60, size: 20),
          onPressed: () {
            // TODO: implementasi logout (hapus token, kembali ke login)
          },
        ),
      ],
    );
  }

  Widget _buildCarGrid() {
    return LayoutBuilder(
      builder: (context, constraints) {
        // 2 kolom untuk layar sempit (HP), 3 kolom kalau lebar layar > 600 (tablet).
        final crossAxisCount = constraints.maxWidth > 600 ? 3 : 2;

        return GridView.builder(
          itemCount: _cars.length,
          gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: crossAxisCount,
            mainAxisSpacing: 14,
            crossAxisSpacing: 14,
            childAspectRatio: 0.85,
          ),
          itemBuilder: (context, index) => _buildCarCard(_cars[index]),
        );
      },
    );
  }

  Widget _buildCarCard(_CarItem car) {
    return Container(
      decoration: BoxDecoration(
        color: const Color(0xFF1A1A1A),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.white12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            child: Container(
              width: double.infinity,
              decoration: const BoxDecoration(
                color: Color(0xFF241C0F),
                borderRadius: BorderRadius.only(
                  topLeft: Radius.circular(12),
                  topRight: Radius.circular(12),
                ),
              ),
              child: const Center(
                child: Icon(
                  Icons.directions_car_filled_outlined,
                  color: Colors.amber,
                  size: 36,
                ),
              ),
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(12, 10, 12, 12),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  car.name,
                  style: const TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                    color: Colors.white,
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 2),
                Text(
                  car.category,
                  style: const TextStyle(fontSize: 12, color: Colors.white60),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _CarItem {
  final String name;
  final String category;
  const _CarItem({required this.name, required this.category});
}