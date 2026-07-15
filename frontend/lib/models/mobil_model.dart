class MobilModel {
  final int id;
  final String namaMobil;
  final String merk;
  final String kategori;
  final String transmisi;
  final int tahun;
  final String platNomor;
  final double hargaSewa;
  final String status;
  final String? gambar;

  MobilModel({
    required this.id,
    required this.namaMobil,
    required this.merk,
    required this.kategori,
    required this.transmisi,
    required this.tahun,
    required this.platNomor,
    required this.hargaSewa,
    required this.status,
    this.gambar,
  });

  factory MobilModel.fromJson(Map<String, dynamic> json) {
    return MobilModel(
      id: json['id'] ?? 0,
      namaMobil: json['nama_mobil']?.toString() ?? '-',
      merk: json['merk']?.toString() ?? '-',
      kategori: json['kategori']?.toString() ?? '-',
      transmisi: json['transmisi']?.toString() ?? '-',
      tahun: int.tryParse(json['tahun']?.toString() ?? '0') ?? 0,
      platNomor: json['plat_nomor']?.toString() ?? '-',
      hargaSewa: double.tryParse(json['harga_sewa']?.toString() ?? '0') ?? 0,
      status: json['status']?.toString() ?? 'tidak diketahui',
      gambar: json['gambar']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nama_mobil': namaMobil,
      'merk': merk,
      'kategori': kategori,
      'transmisi': transmisi,
      'tahun': tahun,
      'plat_nomor': platNomor,
      'harga_sewa': hargaSewa,
      'status': status,
      'gambar': gambar,
    };
  }

  /// Helper untuk cek apakah mobil masih bisa dibooking.
  bool get tersedia => status.toLowerCase() == 'tersedia';
}