class PembayaranModel {
  final int id;
  final int bookingId;
  final String metodeBayar;
  final double jumlahBayar;
  final String status;
  final String? createdAt;

  PembayaranModel({
    required this.id,
    required this.bookingId,
    required this.metodeBayar,
    required this.jumlahBayar,
    required this.status,
    this.createdAt,
  });

  factory PembayaranModel.fromJson(Map<String, dynamic> json) {
    return PembayaranModel(
      id: json['id'] ?? 0,
      bookingId: json['booking_id'] ?? 0,
      metodeBayar: json['metode_bayar']?.toString() ?? '-',
      jumlahBayar: double.tryParse(json['jumlah_bayar']?.toString() ?? '0') ?? 0,
      status: json['status']?.toString() ?? 'pending',
      createdAt: json['created_at']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'booking_id': bookingId,
      'metode_bayar': metodeBayar,
      'jumlah_bayar': jumlahBayar,
      'status': status,
    };
  }

  /// Payload khusus untuk POST /pembayaran — tidak butuh id/status/createdAt
  /// karena itu di-generate backend.
  Map<String, dynamic> toCreatePayload() {
    return {
      'booking_id': bookingId,
      'metode_bayar': metodeBayar,
      'jumlah_bayar': jumlahBayar,
    };
  }

  /// Helper tampilan status pembayaran (pending / lunas / gagal, dst).
  bool get sudahLunas => status.toLowerCase() == 'lunas';
}