<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    
    public function register(Request $request) {

        // validate input register
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'nomor_telepon' => 'required|string',
            'nomor_sim' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        // create new user
        $user = User::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'nomor_telepon' => $request->nomor_telepon,
            'nomor_sim' => $request->nomor_sim,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($user, 201);
    }

    public function login(Request $request)
    {
        // validate input login
        $request->validate([
            'nama' => 'required|string',
            'password' => 'required|string',
        ]);

        // check nama from db
        $user = User::where('nama', $request->nama)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json(['token' => $token]);
    }
    
    
    public function logout()
    {
        // Auth::logout();
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Log out sukses']);
    }
    
    public function getUsers()
    {
        // Get all users
        $users = User::all();
    
        return response()->json($users);
    }
}
