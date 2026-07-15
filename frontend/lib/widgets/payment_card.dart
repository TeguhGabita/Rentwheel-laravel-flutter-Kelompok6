import 'package:flutter/material.dart';
import '../models/pembayaran_model.dart';
import '../utils/currency.dart';
import '../utils/formatter.dart';

/// Card untuk menampilkan ringkasan satu transaksi pembayaran di list
/// (misal di halaman "Riwayat Pembayaran"). Tap card untuk lihat detail.
class PaymentCard extends StatelessWidget {
  final PembayaranModel pembayaran;
  final VoidCallback? onTap;

  const PaymentCard({
    super.key,
    required this.pembayaran,
    this.onTap,
  });

  Color _statusColor() {
    switch (pembayaran.status.toLowerCase()) {
      case 'lunas':
      case 'berhasil':
        return Colors.green;
      case 'pending':
      case 'menunggu':
        return Colors.orange;
      case 'gagal':
      case 'dibatalkan':
        return Colors.redAccent;
      default:
        return Colors.grey;
    }
  }

  IconData _metodeIcon() {
    switch (pembayaran.metodeBayar.toLowerCase()) {
      case 'transfer bank':
        return Icons.account_balance_outlined;
      case 'cash':
        return Icons.payments_outlined;
      case 'e-wallet':
        return Icons.account_balance_wallet_outlined;
      default:
        return Icons.receipt_long_outlined;
    }
  }

  @override
  Widget build(BuildContext context) {
    return InkWell(
      borderRadius: BorderRadius.circular(12),
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
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
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: Colors.amber.withValues(alpha: 0.15),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(_metodeIcon(), size: 20, color: Colors.amber.shade800),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        pembayaran.metodeBayar,
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                        decoration: BoxDecoration(
                          color: _statusColor().withValues(alpha: 0.15),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          pembayaran.status,
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                            color: _statusColor(),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 4),
                  if (pembayaran.createdAt != null)
                    Text(
                      Formatters.tanggalIndo(pembayaran.createdAt),
                      style: const TextStyle(fontSize: 12, color: Colors.grey),
                    ),
                  const SizedBox(height: 8),
                  Text(
                    Currency.rupiah(pembayaran.jumlahBayar),
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Colors.green,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}