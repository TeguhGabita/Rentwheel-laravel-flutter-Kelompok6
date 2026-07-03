import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

/// Sesuaikan base URL ini tergantung tempat testing:
/// - Android Emulator : http://10.0.2.2:8000/api
/// - iOS Simulator    : http://localhost:8000/api
/// - HP fisik (WiFi)  : http://<IP-laptop-kamu>:8000/api
class ApiService {
  static const String baseUrl = "http://10.0.2.2:8000/api";

  Future<String?> _getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('token');
  }

  Future<Map<String, String>> _headers() async {
    final token = await _getToken();
    return {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  /// Login ke backend Laravel (Sanctum), simpan token di local storage
  Future<bool> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Accept': 'application/json'},
      body: {'email': email, 'password': password},
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('token', data['token']);
      return true;
    }
    return false;
  }

  /// Ambil daftar mobil dari endpoint /api/mobils
  Future<List<dynamic>> getMobils() async {
    final response = await http.get(
      Uri.parse('$baseUrl/mobils'),
      headers: await _headers(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['data'] ?? data;
    }
    throw Exception('Gagal mengambil data mobil');
  }

  /// Buat booking baru lewat endpoint /api/bookings
  Future<bool> createBooking({
    required int mobilId,
    required int pelangganId,
    required String tanggalMulai,
    required String tanggalSelesai,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/bookings'),
      headers: await _headers(),
      body: jsonEncode({
        'mobil_id': mobilId,
        'pelanggan_id': pelangganId,
        'tanggal_mulai': tanggalMulai,
        'tanggal_selesai': tanggalSelesai,
      }),
    );

    return response.statusCode == 201 || response.statusCode == 200;
  }
}
