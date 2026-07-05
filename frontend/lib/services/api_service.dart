import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  // Android emulator pakai 10.0.2.2, BUKAN 127.0.0.1
  // Kalau test di device fisik / iOS simulator, ganti ke IP LAN laptop, misal 'http://192.168.1.5:8000/api'
  static const String baseUrl = 'http://127.0.0.1:8000/api';

  static Future<String?> _getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('token');
  }

  static Future<void> _saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('token', token);
  }

  /// Simpan data user (nama, email, role) ke local storage supaya
  /// bisa dipakai lagi tanpa perlu login ulang (misal setelah restart app).
  static Future<void> _saveUser(Map<String, dynamic> user) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('user', jsonEncode(user));
  }

  /// Ambil data user yang tersimpan (null kalau belum pernah login).
  static Future<Map<String, dynamic>?> getSavedUser() async {
    final prefs = await SharedPreferences.getInstance();
    final userStr = prefs.getString('user');
    if (userStr == null) return null;
    return jsonDecode(userStr) as Map<String, dynamic>;
  }

  static Future<void> clearToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('token');
    await prefs.remove('user');
  }

  static Map<String, String> _headers(String? token) => {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        if (token != null) 'Authorization': 'Bearer $token',
      };

  /// Login. Selalu return Map dengan key 'success' (bool) dan 'message' (String).
  /// Kalau sukses, juga ada key 'user' (berisi id, name, email, role) dan
  /// token + data user sudah otomatis tersimpan ke local storage.
  static Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final res = await http.post(
        Uri.parse('$baseUrl/login'),
        headers: _headers(null),
        body: jsonEncode({'email': email, 'password': password}),
      );

      final data = jsonDecode(res.body);

      if (res.statusCode == 200 && data['token'] != null) {
        await _saveToken(data['token']);
        if (data['user'] != null) {
          await _saveUser(data['user']);
        }
        return {
          'success': true,
          'message': 'Login berhasil',
          'user': data['user'],
        };
      }

      // Laravel validation error (422) biasanya taruh pesan di data['message'] atau data['errors']
      String message = data['message'] ?? 'Login gagal, periksa email/password.';
      if (data['errors'] != null) {
        final errors = data['errors'] as Map<String, dynamic>;
        final firstError = errors.values.first;
        if (firstError is List && firstError.isNotEmpty) {
          message = firstError.first.toString();
        }
      }

      return {'success': false, 'message': message};
    } catch (e) {
      return {
        'success': false,
        'message': 'Tidak bisa terhubung ke server. Cek koneksi atau URL API.',
      };
    }
  }

  static Future<Map<String, dynamic>> register(
      String name, String email, String password, String passwordConfirmation) async {
    try {
      final res = await http.post(
        Uri.parse('$baseUrl/register'),
        headers: _headers(null),
        body: jsonEncode({
          'name': name,
          'email': email,
          'password': password,
          'password_confirmation': passwordConfirmation,
        }),
      );

      final data = jsonDecode(res.body);

      if (res.statusCode == 201 && data['token'] != null) {
        await _saveToken(data['token']);
        if (data['user'] != null) {
          await _saveUser(data['user']);
        }
        return {'success': true, 'message': 'Registrasi berhasil', 'user': data['user']};
      }

      String message = data['message'] ?? 'Registrasi gagal.';
      if (data['errors'] != null) {
        final errors = data['errors'] as Map<String, dynamic>;
        final firstError = errors.values.first;
        if (firstError is List && firstError.isNotEmpty) {
          message = firstError.first.toString();
        }
      }

      return {'success': false, 'message': message};
    } catch (e) {
      return {
        'success': false,
        'message': 'Tidak bisa terhubung ke server. Cek koneksi atau URL API.',
      };
    }
  }

  static Future<Map<String, dynamic>> getMobil() async {
    try {
      final token = await _getToken();
      final res = await http.get(
        Uri.parse('$baseUrl/mobil'),
        headers: _headers(token),
      );

      if (res.statusCode == 200) {
        return {'success': true, 'data': jsonDecode(res.body)};
      }

      return {'success': false, 'message': 'Gagal mengambil data mobil.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  static Future<Map<String, dynamic>> getBookings() async {
    try {
      final token = await _getToken();
      final res = await http.get(
        Uri.parse('$baseUrl/bookings'),
        headers: _headers(token),
      );

      if (res.statusCode == 200) {
        return {'success': true, 'data': jsonDecode(res.body)};
      }

      return {'success': false, 'message': 'Gagal mengambil data booking.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  static Future<Map<String, dynamic>> createBooking(Map<String, dynamic> payload) async {
    try {
      final token = await _getToken();
      final res = await http.post(
        Uri.parse('$baseUrl/bookings'),
        headers: _headers(token),
        body: jsonEncode(payload),
      );

      final data = jsonDecode(res.body);

      if (res.statusCode == 200 || res.statusCode == 201) {
        return {'success': true, 'data': data};
      }

      String message = data['message'] ?? 'Gagal membuat booking.';
      if (data['errors'] != null) {
        final errors = data['errors'] as Map<String, dynamic>;
        final firstError = errors.values.first;
        if (firstError is List && firstError.isNotEmpty) {
          message = firstError.first.toString();
        }
      }

      return {'success': false, 'message': message};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  static Future<Map<String, dynamic>> logout() async {
    try {
      final token = await _getToken();
      await http.post(
        Uri.parse('$baseUrl/logout'),
        headers: _headers(token),
      );
      await clearToken();
      return {'success': true, 'message': 'Logout berhasil'};
    } catch (e) {
      await clearToken(); // tetap clear token lokal walau request gagal
      return {'success': true, 'message': 'Logout berhasil (lokal)'};
    }
  }
}