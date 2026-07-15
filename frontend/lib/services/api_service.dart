import 'dart:convert';
import 'dart:typed_data';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  // Android emulator pakai 10.0.2.2, BUKAN 127.0.0.1
  // Kalau test di device fisik / iOS simulator, ganti ke IP LAN laptop, misal 'http://192.168.1.34:8000/api'
  static const String baseUrl = "http://127.0.0.1:8000/api";

  static Future<String?> _getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('token');
  }

  /// Public getter supaya service lain (mobil_service, pembayaran_service, dll)
  /// bisa ambil token tanpa akses field private.
  static Future<String?> getToken() async {
    return await _getToken();
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

  /// Helper generik: decode body dengan aman, kalau gagal (misal body kosong
  /// atau bukan JSON) balikin Map kosong supaya tidak crash.
  static Map<String, dynamic> _safeDecode(String body) {
    try {
      final decoded = jsonDecode(body);
      if (decoded is Map<String, dynamic>) return decoded;
      return {'data': decoded};
    } catch (e) {
      return {};
    }
  }

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

  /// Perbaikan: sekarang aman terhadap response Laravel yang berbentuk
  /// pagination object ({"data": [...], "current_page": 1, ...}) maupun
  /// array langsung ([...]) - konsisten dengan getKategori()/getUser()/getNotifikasi().
  static Future<Map<String, dynamic>> getMobil() async {
    try {
      final token = await _getToken();
      final res = await http.get(
        Uri.parse('$baseUrl/mobil'),
        headers: _headers(token),
      );

      if (res.statusCode == 200) {
        final decoded = jsonDecode(res.body);
        final data = decoded is List
            ? decoded
            : (decoded is Map && decoded['data'] is List ? decoded['data'] : []);
        return {'success': true, 'data': data};
      }

      return {'success': false, 'message': 'Gagal mengambil data mobil.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  // ==================== MOBIL (CRUD) ====================
  // TODO BACKEND: pastikan route berikut sudah ada di routes/api.php Laravel:
  //   Route::post('/mobil', [MobilController::class, 'store']);
  //   Route::put('/mobil/{id}', [MobilController::class, 'update']);
  //   Route::delete('/mobil/{id}', [MobilController::class, 'destroy']);

  static Future<Map<String, dynamic>> addMobil(Map<String, dynamic> payload) async {
    try {
      final token = await _getToken();
      final res = await http.post(
        Uri.parse('$baseUrl/mobil'),
        headers: _headers(token),
        body: jsonEncode(payload),
      );

      final data = _safeDecode(res.body);

      if (res.statusCode == 200 || res.statusCode == 201) {
        return {'success': true, 'data': data};
      }
      return {'success': false, 'message': data['message'] ?? 'Gagal menambah mobil.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  static Future<Map<String, dynamic>> updateMobil(int id, Map<String, dynamic> payload) async {
    try {
      final token = await _getToken();
      final res = await http.put(
        Uri.parse('$baseUrl/mobil/$id'),
        headers: _headers(token),
        body: jsonEncode(payload),
      );

      final data = _safeDecode(res.body);

      if (res.statusCode == 200) {
        return {'success': true, 'data': data};
      }
      return {'success': false, 'message': data['message'] ?? 'Gagal mengupdate mobil.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  static Future<Map<String, dynamic>> deleteMobil(int id) async {
    try {
      final token = await _getToken();
      final res = await http.delete(
        Uri.parse('$baseUrl/mobil/$id'),
        headers: _headers(token),
      );

      if (res.statusCode == 200 || res.statusCode == 204) {
        return {'success': true};
      }
      final data = _safeDecode(res.body);
      return {'success': false, 'message': data['message'] ?? 'Gagal menghapus mobil.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  // ==================== KATEGORI (CRUD) ====================
  // Route asli di backend Laravel bernama '/kategori-mobil' (lihat routes/api.php,
  // controller: App\Http\Controllers\Api\KategoriMobilController), BUKAN '/kategori'.

  static Future<Map<String, dynamic>> getKategori() async {
    try {
      final token = await _getToken();
      final res = await http.get(
        Uri.parse('$baseUrl/kategori-mobil'),
        headers: _headers(token),
      );

      if (res.statusCode == 200) {
        final decoded = jsonDecode(res.body);
        final data = decoded is List ? decoded : (decoded['data'] ?? decoded);
        return {'success': true, 'data': data};
      }
      return {'success': false, 'message': 'Gagal mengambil data kategori.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  static Future<Map<String, dynamic>> addKategori(Map<String, dynamic> payload) async {
    try {
      final token = await _getToken();
      final res = await http.post(
        Uri.parse('$baseUrl/kategori-mobil'),
        headers: _headers(token),
        body: jsonEncode(payload),
      );

      final data = _safeDecode(res.body);

      if (res.statusCode == 200 || res.statusCode == 201) {
        return {'success': true, 'data': data};
      }
      return {'success': false, 'message': data['message'] ?? 'Gagal menambah kategori.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  static Future<Map<String, dynamic>> updateKategori(int id, Map<String, dynamic> payload) async {
    try {
      final token = await _getToken();
      final res = await http.put(
        Uri.parse('$baseUrl/kategori-mobil/$id'),
        headers: _headers(token),
        body: jsonEncode(payload),
      );

      final data = _safeDecode(res.body);

      if (res.statusCode == 200) {
        return {'success': true, 'data': data};
      }
      return {'success': false, 'message': data['message'] ?? 'Gagal mengupdate kategori.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  static Future<Map<String, dynamic>> deleteKategori(int id) async {
    try {
      final token = await _getToken();
      final res = await http.delete(
        Uri.parse('$baseUrl/kategori-mobil/$id'),
        headers: _headers(token),
      );

      if (res.statusCode == 200 || res.statusCode == 204) {
        return {'success': true};
      }
      final data = _safeDecode(res.body);
      return {'success': false, 'message': data['message'] ?? 'Gagal menghapus kategori.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  // ==================== USER (CRUD) ====================
  // TODO BACKEND: pastikan route berikut ada di routes/api.php:
  //   Route::get('/user', [UserController::class, 'index']);
  //   Route::post('/user', [UserController::class, 'store']);
  //   Route::put('/user/{id}', [UserController::class, 'update']);
  //   Route::delete('/user/{id}', [UserController::class, 'destroy']);

  static Future<Map<String, dynamic>> getUser() async {
    try {
      final token = await _getToken();
      final res = await http.get(
        Uri.parse('$baseUrl/user'),
        headers: _headers(token),
      );

      if (res.statusCode == 200) {
        final decoded = jsonDecode(res.body);
        final data = decoded is List ? decoded : (decoded['data'] ?? decoded);
        return {'success': true, 'data': data};
      }
      return {'success': false, 'message': 'Gagal mengambil data user.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  static Future<Map<String, dynamic>> addUser(Map<String, dynamic> payload) async {
    try {
      final token = await _getToken();
      final res = await http.post(
        Uri.parse('$baseUrl/user'),
        headers: _headers(token),
        body: jsonEncode(payload),
      );

      final data = _safeDecode(res.body);

      if (res.statusCode == 200 || res.statusCode == 201) {
        return {'success': true, 'data': data};
      }
      return {'success': false, 'message': data['message'] ?? 'Gagal menambah user.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  static Future<Map<String, dynamic>> updateUser(int id, Map<String, dynamic> payload) async {
    try {
      final token = await _getToken();
      final res = await http.put(
        Uri.parse('$baseUrl/user/$id'),
        headers: _headers(token),
        body: jsonEncode(payload),
      );

      final data = _safeDecode(res.body);

      if (res.statusCode == 200) {
        return {'success': true, 'data': data};
      }
      return {'success': false, 'message': data['message'] ?? 'Gagal mengupdate user.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  static Future<Map<String, dynamic>> deleteUser(int id) async {
    try {
      final token = await _getToken();
      final res = await http.delete(
        Uri.parse('$baseUrl/user/$id'),
        headers: _headers(token),
      );

      if (res.statusCode == 200 || res.statusCode == 204) {
        return {'success': true};
      }
      final data = _safeDecode(res.body);
      return {'success': false, 'message': data['message'] ?? 'Gagal menghapus user.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  // ==================== LAPORAN ====================
  // Route: GET /laporan -> LaporanController@index
  // Response asli dari Laravel SUDAH berbentuk {'success': true, 'data': {...}},
  // jadi di sini TIDAK boleh dibungkus lagi jadi {'data': jsonDecode(res.body)}
  // (itu penyebab total_pendapatan/total_booking/transaksi selalu kosong di UI,
  // karena LaporanScreen membaca result['data']['total_pendapatan'] dst, padahal
  // sebelumnya result['data'] isinya {success, data} lagi, bukan angka aslinya).

  static Future<Map<String, dynamic>> getLaporan() async {
    try {
      final token = await _getToken();
      final res = await http.get(
        Uri.parse('$baseUrl/laporan'),
        headers: _headers(token),
      );

      if (res.statusCode == 200) {
        final decoded = jsonDecode(res.body);
        // Pass-through langsung: decoded sudah berbentuk {success, data}.
        if (decoded is Map<String, dynamic>) {
          return decoded;
        }
        return {'success': true, 'data': decoded};
      }
      return {'success': false, 'message': 'Gagal mengambil data laporan.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  /// PUT /booking/{id}/status — ubah status booking (dipesan/berjalan/selesai/batal).
  /// Ditambahkan supaya admin bisa mengubah status booking dari halaman Laporan.
  static Future<Map<String, dynamic>> updateBookingStatus(dynamic id, String status) async {
    try {
      final token = await _getToken();
      final res = await http.put(
        Uri.parse('$baseUrl/booking/$id/status'),
        headers: _headers(token),
        body: jsonEncode({'status': status}),
      );

      final data = _safeDecode(res.body);

      if (res.statusCode == 200) {
        return {'success': true, 'data': data};
      }

      return {
        'success': false,
        'message': data['message'] ?? 'Gagal mengubah status booking.',
      };
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  /// PUT /pembayaran/{id}/status — admin acc/tolak pembayaran
  /// (status_bayar: pending / lunas / ditolak). Dipakai dari halaman Laporan.
  static Future<Map<String, dynamic>> updatePembayaranStatus(dynamic id, String statusBayar) async {
    try {
      final token = await _getToken();
      final res = await http.put(
        Uri.parse('$baseUrl/pembayaran/$id/status'),
        headers: _headers(token),
        body: jsonEncode({'status_bayar': statusBayar}),
      );

      final data = _safeDecode(res.body);

      if (res.statusCode == 200) {
        return {'success': true, 'data': data};
      }

      return {
        'success': false,
        'message': data['message'] ?? 'Gagal mengubah status pembayaran.',
      };
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  static Future<Map<String, dynamic>> getBookings() async {
    try {
      final token = await _getToken();

      final res = await http.get(
        Uri.parse('$baseUrl/booking'),
        headers: _headers(token),
      );

      if (res.statusCode == 200) {
        return {
          'success': true,
          'data': jsonDecode(res.body),
        };
      }

      return {
        'success': false,
        'message': 'Gagal mengambil data booking.'
      };
    } catch (e) {
      return {
        'success': false,
        'message': 'Tidak bisa terhubung ke server.'
      };
    }
  }

  static Future<Map<String, dynamic>> createBooking(
      Map<String, dynamic> payload) async {
    try {
      final token = await _getToken();

      print("TOKEN = $token");
      print("PAYLOAD = $payload");

      final res = await http.post(
        Uri.parse('$baseUrl/booking'),
        headers: _headers(token),
        body: jsonEncode(payload),
      );

      print("STATUS = ${res.statusCode}");
      print("BODY = ${res.body}");

      final data = _safeDecode(res.body);

      if (res.statusCode == 200 || res.statusCode == 201) {
        return {
          'success': true,
          'data': data,
        };
      }

      return {
        'success': false,
        'message': data['message'],
        'errors': data['errors'],
      };
    } catch (e) {
      print(e);
      return {
        'success': false,
        'message': e.toString(),
      };
    }
  }

  /// PUT /profile — update nama, email, no_hp, no_ktp, alamat user yang login.
  static Future<Map<String, dynamic>> updateProfile(Map<String, dynamic> payload) async {
    try {
      final token = await _getToken();
      final res = await http.put(
        Uri.parse('$baseUrl/profile'),
        headers: _headers(token),
        body: jsonEncode(payload),
      );

      final data = _safeDecode(res.body);

      if (res.statusCode == 200) {
        if (data['user'] != null) {
          await _saveUser(Map<String, dynamic>.from(data['user']));
        }
        return {'success': true, 'user': data['user'], 'message': data['message']};
      }

      return {'success': false, 'message': data['message'] ?? 'Gagal mengupdate profil.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  /// PUT /profile/password — ganti password user yang login.
  static Future<Map<String, dynamic>> changePassword({
    required String currentPassword,
    required String newPassword,
    required String newPasswordConfirmation,
  }) async {
    try {
      final token = await _getToken();
      final res = await http.put(
        Uri.parse('$baseUrl/profile/password'),
        headers: _headers(token),
        body: jsonEncode({
          'current_password': currentPassword,
          'new_password': newPassword,
          'new_password_confirmation': newPasswordConfirmation,
        }),
      );

      final data = _safeDecode(res.body);

      if (res.statusCode == 200) {
        return {'success': true, 'message': data['message'] ?? 'Password berhasil diubah.'};
      }

      return {'success': false, 'message': data['message'] ?? 'Gagal mengubah password.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  static Future<Map<String, dynamic>> me() async {
    try {
      final token = await _getToken();

      final res = await http.get(
        Uri.parse('$baseUrl/me'),
        headers: _headers(token),
      );

      final data = jsonDecode(res.body);

      if (res.statusCode == 200) {
        if (data['user'] != null) {
          await _saveUser(data['user']);
        }

        return {
          'success': true,
          'user': data['user'],
        };
      }

      return {
        'success': false,
        'message': data['message'],
      };
    } catch (e) {
      return {
        'success': false,
        'message': e.toString(),
      };
    }
  }

  static Future<Map<String, dynamic>> getDashboard() async {
    try {
      final token = await _getToken();

      final res = await http.get(
        Uri.parse('$baseUrl/dashboard'),
        headers: _headers(token),
      );

      final data = jsonDecode(res.body);

      if (res.statusCode == 200) {
        return {
          'success': true,
          'data': data,
        };
      }

      return {
        'success': false,
        'message': data['message'],
      };
    } catch (e) {
      return {
        'success': false,
        'message': e.toString(),
      };
    }
  }

  /// POST /pembayaran — buat pembayaran baru, opsional dengan bukti transfer.
  /// Dipakai bytes (Uint8List) bukan path file, supaya jalan di Mobile & Web
  /// sekaligus (MultipartFile.fromPath TIDAK didukung di Flutter Web).
  static Future<Map<String, dynamic>> createPembayaran(
    Map<String, dynamic> payload, {
    Uint8List? buktiTransferBytes,
    String? filename,
  }) async {
    try {
      final token = await _getToken();

      // Kalau ada file bukti transfer, kirim pakai multipart/form-data
      if (buktiTransferBytes != null) {
        final request = http.MultipartRequest(
          'POST',
          Uri.parse('$baseUrl/pembayaran'),
        );

        request.headers.addAll({
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        });

        // Semua field payload dikirim sebagai string (format multipart)
        payload.forEach((key, value) {
          request.fields[key] = value.toString();
        });

        // fromBytes dipakai (bukan fromPath) supaya jalan juga di Flutter Web
        request.files.add(
          http.MultipartFile.fromBytes(
            'bukti_pembayaran',
            buktiTransferBytes,
            filename: filename ?? 'bukti_transfer.jpg',
          ),
        );

        final streamedRes = await request.send();
        final res = await http.Response.fromStream(streamedRes);
        final data = _safeDecode(res.body);

        if (res.statusCode == 200 || res.statusCode == 201) {
          return {'success': true, 'data': data};
        }
        return {'success': false, 'message': data['message'] ?? 'Gagal membuat pembayaran.'};
      }

      // Kalau tidak ada file, kirim JSON biasa seperti sebelumnya
      final res = await http.post(
        Uri.parse('$baseUrl/pembayaran'),
        headers: _headers(token),
        body: jsonEncode(payload),
      );

      final data = jsonDecode(res.body);

      if (res.statusCode == 200 || res.statusCode == 201) {
        return {'success': true, 'data': data};
      }

      return {'success': false, 'message': data['message']};
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  static Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();

    await prefs.remove('token');
    await prefs.remove('user');
  }

  // ==================== NOTIFIKASI ====================
  // TODO BACKEND: pastikan route berikut ada di routes/api.php:
  //   Route::get('/notifikasi', [NotifikasiController::class, 'index']);
  //   Route::put('/notifikasi/{id}/baca', [NotifikasiController::class, 'baca']);
  //   Route::put('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua']);

  /// Ambil daftar notifikasi user yang login.
  /// FIX: response backend berbentuk {'success', 'data', 'unread_count'}.
  /// Sebelumnya 'unread_count' tidak ikut diteruskan, jadi badge notifikasi
  /// di HomeScreen selalu 0 walau ada notifikasi yang belum dibaca.
  static Future<Map<String, dynamic>> getNotifikasi() async {
    try {
      final token = await _getToken();
      final res = await http.get(
        Uri.parse('$baseUrl/notifikasi'),
        headers: _headers(token),
      );

      if (res.statusCode == 200) {
        final decoded = jsonDecode(res.body);
        final data = decoded is List ? decoded : (decoded['data'] ?? decoded);
        final unreadCount = decoded is Map ? (decoded['unread_count'] ?? 0) : 0;
        return {'success': true, 'data': data, 'unread_count': unreadCount};
      }
      return {'success': false, 'message': 'Gagal mengambil notifikasi.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  /// Tandai satu notifikasi sebagai sudah dibaca.
  /// Terima `id` sebagai int atau String (disesuaikan dengan tipe id dari JSON/UI
  /// yang dipakai di home_screen.dart).
  static Future<Map<String, dynamic>> bacaNotifikasi(dynamic id) async {
    try {
      final token = await _getToken();
      final res = await http.post(
        Uri.parse('$baseUrl/notifikasi/$id/baca'),
        headers: _headers(token),
      );

      if (res.statusCode == 200) {
        return {'success': true};
      }
      final data = _safeDecode(res.body);
      return {'success': false, 'message': data['message'] ?? 'Gagal menandai notifikasi.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }

  /// Tandai semua notifikasi sebagai sudah dibaca.
  static Future<Map<String, dynamic>> bacaSemuaNotifikasi() async {
    try {
      final token = await _getToken();
      final res = await http.post(
        Uri.parse('$baseUrl/notifikasi/baca-semua'),
        headers: _headers(token),
      );

      if (res.statusCode == 200) {
        return {'success': true};
      }
      final data = _safeDecode(res.body);
      return {'success': false, 'message': data['message'] ?? 'Gagal menandai semua notifikasi.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server.'};
    }
  }
}