/// Model data user, merepresentasikan hasil response dari API Laravel
/// (misal endpoint GET /api/auth/me atau POST /api/auth/login).
///
/// Sesuaikan field di bawah dengan kolom tabel `users` di database kamu.
/// Field `role` opsional — isi kalau tabel users punya kolom role langsung,
/// atau kalau pakai Spatie Permission, ambil dari `roles` array di response.
class UserModel {
  final int id;
  final String name;
  final String email;
  final String? role;

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    this.role,
  });

  /// Membuat UserModel dari JSON hasil response API.
  ///
  /// Contoh response Laravel:
  /// {
  ///   "success": true,
  ///   "data": {
  ///     "id": 2,
  ///     "name": "admin",
  ///     "email": "admin@rentwheel.test",
  ///     "role": "admin"
  ///   }
  /// }
  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'],
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      role: json['role'], // null kalau tidak ada field ini di response
    );
  }

  /// Mengubah UserModel kembali ke bentuk JSON (misal untuk disimpan
  /// ke local storage / shared_preferences).
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'role': role,
    };
  }

  bool get isAdmin => role == 'admin';
}