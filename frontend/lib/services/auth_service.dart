import 'api_service.dart';

class AuthService {
  Future<Map<String, dynamic>> me() async {
    final result = await ApiService.me();
    if (result['success'] == true && result['user'] != null) {
      return result['user'] as Map<String, dynamic>;
    }
    throw Exception(result['message'] ?? 'Gagal mengambil data user');
  }
}