class BookingModel {
  final int id;
  final int mobilId;
  final String tanggalMulai;
  final String tanggalSelesai;
  final String status;
  final double totalHarga;
  final MobilRingkas? mobil;

  BookingModel({
    required this.id,
    required this.mobilId,
    required this.tanggalMulai,
    required this.tanggalSelesai,
    required this.status,
    required this.totalHarga,
    this.mobil,
  });

  factory BookingModel.fromJson(Map<String, dynamic> json) {
    return BookingModel(
      id: json['id'] ?? 0,
      mobilId: json['mobil_id'] ?? 0,
      tanggalMulai: json['tanggal_mulai']?.toString() ?? '',
      tanggalSelesai: json['tanggal_selesai']?.toString() ?? '',
      status: json['status']?.toString() ?? 'pending',
      totalHarga: double.tryParse(json['total_harga']?.toString() ?? '0') ?? 0,
      mobil: json['mobil'] != null
          ? MobilRingkas.fromJson(json['mobil'])
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'mobil_id': mobilId,
      'tanggal_mulai': tanggalMulai,
      'tanggal_selesai': tanggalSelesai,
      'status': status,
      'total_harga': totalHarga,
    };
  }
}

/// Data mobil ringkas yang ikut ter-nested di response booking
/// (nama_mobil, merk, harga_sewa) — dipakai untuk tampilan list booking
/// tanpa perlu fetch detail mobil lagi.
class MobilRingkas {
  final int id;
  final String namaMobil;
  final String merk;
  final double hargaSewa;

  MobilRingkas({
    required this.id,
    required this.namaMobil,
    required this.merk,
    required this.hargaSewa,
  });

  factory MobilRingkas.fromJson(Map<String, dynamic> json) {
    return MobilRingkas(
      id: json['id'] ?? 0,
      namaMobil: json['nama_mobil']?.toString() ?? '-',
      merk: json['merk']?.toString() ?? '-',
      hargaSewa: double.tryParse(json['harga_sewa']?.toString() ?? '0') ?? 0,
    );
  }
}