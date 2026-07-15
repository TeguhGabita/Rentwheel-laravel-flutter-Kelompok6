import 'package:flutter/material.dart';
import '../booking/booking_screen.dart';

class DetailMobilScreen extends StatelessWidget {
  final Map<String, dynamic> mobil;

  const DetailMobilScreen({
    super.key,
    required this.mobil,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(mobil["nama_mobil"] ?? "Detail Mobil"),
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [

            Container(
              height: 220,
              width: double.infinity,
              color: Colors.grey.shade300,
              child: const Icon(
                Icons.directions_car,
                size: 120,
                color: Colors.grey,
              ),
            ),

            Padding(
              padding: const EdgeInsets.all(16),

              child: Column(
                children: [

                  _item("Nama Mobil", mobil["nama_mobil"]),

                  _item("Merk", mobil["merk"]),

                  _item("Kategori", mobil["kategori"]),

                  _item("Transmisi", mobil["transmisi"]),

                  _item("Tahun", mobil["tahun"]),

                  _item("Plat Nomor", mobil["plat_nomor"]),

                  _item("Harga Sewa",
                      "Rp ${mobil["harga_sewa"]}/Hari"),

                  _item("Status", mobil["status"]),

                  const SizedBox(height: 30),

                  SizedBox(
                    width: double.infinity,
                    height: 50,
                    child: ElevatedButton(
                      child: const Text("Booking Sekarang"),
                      onPressed: () {

                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => BookingScreen(
                              mobilId: mobil["id"],
                            ),
                          ),
                        );

                      },
                    ),
                  )

                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _item(String title, dynamic value) {

    return Card(
      child: ListTile(
        title: Text(title),
        subtitle: Text(value?.toString() ?? "-"),
      ),
    );

  }

}