import 'dart:convert';
import 'package:http/http.dart' as http;
import '../services/api_service.dart';

class MobilService {
  static Future<List<dynamic>> getMobil() async {
    try {
      final result = await ApiService.getMobil();

      if (result['success'] == true) {
        if (result['data'] is List) {
          return result['data'];
        }

        if (result['data']['data'] != null) {
          return result['data']['data'];
        }
      }

      return [];
    } catch (e) {
      return [];
    }
  }

  static Future<Map<String, dynamic>?> getDetailMobil(int id) async {
    try {
      final token = await ApiService.getToken();

      final response = await http.get(
        Uri.parse('${ApiService.baseUrl}/mobil/$id'),
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

  /// Tambah mobil baru. Return Map dengan key 'success' (bool) dan
  /// 'message'/'data' tergantung hasilnya.
  static Future<Map<String, dynamic>> addMobil(Map<String, dynamic> payload) async {
    return await ApiService.addMobil(payload);
  }

  /// Update data mobil berdasarkan id.
  static Future<Map<String, dynamic>> updateMobil(int id, Map<String, dynamic> payload) async {
    return await ApiService.updateMobil(id, payload);
  }

  /// Hapus mobil berdasarkan id.
  static Future<Map<String, dynamic>> deleteMobil(int id) async {
    return await ApiService.deleteMobil(id);
  }
}