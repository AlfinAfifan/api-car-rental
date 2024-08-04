<?php

namespace App\Http\Controllers;

use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        try {
            // Validasi input register
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'alamat' => 'required|string',
                'nomor_telepon' => 'required|string',
                'nomor_sim' => 'required|string',
                'password' => 'required|string|min:3',
                'role' => 'required|string',
            ]);

            // Buat pengguna baru
            $user = User::create([
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'],
                'nomor_telepon' => $validated['nomor_telepon'],
                'nomor_sim' => $validated['nomor_sim'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role']
            ]);

            // Kirim respons JSON dengan status 201 Created
            return response()->json([
                'success' => true,
                'message' => 'User successfully registered',
                'data' => $user
            ], 201);

        } catch (ValidationException $e) {
            // Tangani kesalahan validasi dan kirim respons JSON dengan status 422 Unprocessable Entity
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // Tangani kesalahan umum dan kirim respons JSON dengan status 500 Internal Server Error
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while registering user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            // Validasi input login
            $validated = $request->validate([
                'nama' => 'required|string',
                'password' => 'required|string',
            ]);

            // Periksa nama dari database
            $user = User::where('nama', $validated['nama'])->first();
            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $token = $user->createToken('Personal Access Token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'data' => $user
            ]);

        } catch (\Exception $e) {
            // Tangani kesalahan umum
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        try {
            // Hapus semua token pengguna saat ini
            auth()->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);

        } catch (\Exception $e) {
            // Tangani kesalahan umum
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUsers()
    {
        try {
            // Ambil semua pengguna
            $users = User::all();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            // Tangani kesalahan umum
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching users',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
