import 'api_service.dart';

class BookingService {

  /// Ambil semua booking user
  static Future<Map<String, dynamic>> getBookings() async {
    return await ApiService.getBookings();
  }

  /// Buat booking baru
  static Future<Map<String, dynamic>> createBooking({
    required int mobilId,
    required String tanggalMulai,
    required String tanggalSelesai,
  }) async {

    return await ApiService.createBooking({

      "mobil_id": mobilId,

      "tanggal_mulai": tanggalMulai,

      "tanggal_selesai": tanggalSelesai,

    });

  }

}