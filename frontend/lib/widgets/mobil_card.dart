import 'package:flutter/material.dart';
import '../models/mobil_model.dart';
import '../utils/currency.dart';

/// Card untuk menampilkan ringkasan satu mobil di list (home screen,
/// hasil pencarian, dll). Tap card untuk lihat detail mobil.
class MobilCard extends StatelessWidget {
  final MobilModel mobil;
  final VoidCallback? onTap;
  final VoidCallback? onBookingTap;

  const MobilCard({
    super.key,
    required this.mobil,
    this.onTap,
    this.onBookingTap,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      borderRadius: BorderRadius.circular(12),
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: Colors.grey.shade200),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.04),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Thumbnail — pakai icon dulu, ganti Image.network kalau
            // backend sudah punya field gambar mobil.
            Container(
              width: 90,
              height: 90,
              margin: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: Colors.grey.shade100,
                borderRadius: BorderRadius.circular(10),
              ),
              child: const Icon(
                Icons.directions_car,
                size: 40,
                color: Colors.grey,
              ),
            ),
            Expanded(
              child: Padding(
                padding: const EdgeInsets.only(right: 12, top: 10, bottom: 10),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      mobil.namaMobil,
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w600,
                      ),
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 2),
                    Text(
                      '${mobil.merk} · ${mobil.transmisi}',
                      style: const TextStyle(fontSize: 12, color: Colors.grey),
                    ),
                    const SizedBox(height: 8),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          Currency.rupiahPerHari(mobil.hargaSewa),
                          style: const TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.bold,
                            color: Colors.green,
                          ),
                        ),
                        if (onBookingTap != null)
                          SizedBox(
                            height: 32,
                            child: ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                padding: const EdgeInsets.symmetric(horizontal: 12),
                                textStyle: const TextStyle(fontSize: 12),
                              ),
                              onPressed: mobil.tersedia ? onBookingTap : null,
                              child: Text(
                                mobil.tersedia ? 'Booking' : 'Tidak tersedia',
                              ),
                            ),
                          ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}