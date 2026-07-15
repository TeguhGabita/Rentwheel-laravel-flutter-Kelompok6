import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class DashboardService {
  // Ganti sesuai alamat Laravel
  static const String baseUrl = "http://10.0.2.2:8000/api";

  static Future<String?> _getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('token');
  }

  static Future<Map<String, dynamic>> getDashboard() async {
    try {
      final token = await _getToken();

      final response = await http.get(
        Uri.parse("$baseUrl/admin/dashboard"),
        headers: {
          "Accept": "application/json",
          "Authorization": "Bearer $token",
        },
      );

      if (response.statusCode == 200) {
        return {
          "success": true,
          "data": jsonDecode(response.body),
        };
      }

      return {
        "success": false,
        "message": "Gagal mengambil dashboard",
      };
    } catch (e) {
      return {
        "success": false,
        "message": e.toString(),
      };
    }
  }
}