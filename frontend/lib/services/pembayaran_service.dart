import 'dart:convert';
import 'package:http/http.dart' as http;
import 'api_service.dart';

class PembayaranService {
  /// Ambil semua pembayaran milik user
  static Future<List<dynamic>> getPembayaran() async {
    try {
      final token = await ApiService.getToken();

      final response = await http.get(
        Uri.parse('${ApiService.baseUrl}/pembayaran'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        if (data is List) {
          return data;
        }

        if (data['data'] != null) {
          return data['data'];
        }
      }

      return [];
    } catch (e) {
      return [];
    }
  }

  /// Simpan pembayaran baru
  static Future<Map<String, dynamic>> createPembayaran({
    required int bookingId,
    required String metodePembayaran,
    required double jumlahBayar,
  }) async {
    try {
      final token = await ApiService.getToken();

      final response = await http.post(
        Uri.parse('${ApiService.baseUrl}/pembayaran'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode({
          'booking_id': bookingId,
          'metode_pembayaran': metodePembayaran,
          'jumlah_bayar': jumlahBayar,
        }),
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 ||
          response.statusCode == 201) {
        return {
          'success': true,
          'data': data,
        };
      }

      return {
        'success': false,
        'message': data['message'] ?? 'Pembayaran gagal.',
      };
    } catch (e) {
      return {
        'success': false,
        'message': 'Tidak dapat terhubung ke server.',
      };
    }
  }

  /// Detail pembayaran
  static Future<Map<String, dynamic>?> detailPembayaran(int id) async {
    try {
      final token = await ApiService.getToken();

      final response = await http.get(
        Uri.parse('${ApiService.baseUrl}/pembayaran/$id'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }

      return null;
    } catch (e) {
      return null;
    }
  }
}