<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'no_hp' => 'nullable|string|max:20',
            'no_ktp' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'no_hp' => $request->no_hp,
            'no_ktp' => $request->no_ktp,
            'alamat' => $request->alamat,
        ]);

        $user->assignRole('user'); // pakai method dari Spatie
        $token = $user->createToken('flutter-token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'no_hp' => $user->no_hp,
                'no_ktp' => $user->no_ktp,
                'alamat' => $user->alamat,
                'role' => $user->getRoleNames()->first(),
            ],
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah',
            ], 401);
        }

        $token = $user->createToken('flutter-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'no_hp' => $user->no_hp,
                'no_ktp' => $user->no_ktp,
                'alamat' => $user->alamat,
                'role' => $user->getRoleNames()->first(), // misal 'admin' atau 'user'
            ],
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    /**
     * GET /me
     * Sebelumnya balikin field langsung tanpa dibungkus key 'user', padahal
     * Flutter (ApiService.me()) baca data['user']. Sudah diperbaiki + field
     * no_hp/no_ktp/alamat ditambahkan supaya bisa ditampilkan di halaman Profil.
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'no_hp' => $user->no_hp,
                'no_ktp' => $user->no_ktp,
                'alamat' => $user->alamat,
                'role' => $user->getRoleNames()->first(),
            ],
        ]);
    }

    /**
     * PUT /profile
     * Update nama, email, no_hp, no_ktp, alamat milik user yang sedang login.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
            'no_ktp' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'no_ktp' => $request->no_ktp,
            'alamat' => $request->alamat,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diupdate',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'no_hp' => $user->no_hp,
                'no_ktp' => $user->no_ktp,
                'alamat' => $user->alamat,
                'role' => $user->getRoleNames()->first(),
            ],
        ]);
    }

    /**
     * PUT /profile/password
     * Ganti password milik user yang sedang login. Wajib isi password lama
     * yang benar dulu sebelum bisa ganti ke password baru.
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.',
        ]);
    }
}
