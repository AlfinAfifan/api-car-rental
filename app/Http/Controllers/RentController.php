<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use App\Models\Rent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RentController extends Controller
{
    public function rentMobil(Request $request)
{
    $request->validate([
        // Validate based plat nomor
        'nomor_plat' => 'required|string|exists:mobil,nomor_plat',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
    ]);

    // Find mobil based plat nomoe
    $car = Mobil::where('nomor_plat', $request->nomor_plat)->first();

    if (!$car) {
        return response()->json(['error' => 'Mobil tidak ditemukan'], 404);
    }

    // Verify if mobil available
    $rentals = Rent::where('mobil_id', $car->_id)
                    ->where(function($query) use ($request) {
                        $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                              ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
                    })
                    ->exists();

    if ($rentals) {
        return response()->json(['error' => 'Mobil tidak tersedia'], 400);
    }

    // Create new rent
    $rental = Rent::create([
        'user_id' => $request->user()->id,
        'mobil_id' => $car->_id,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
    ]);

    return response()->json($rental, 201);
}



    public function returnMobil(Request $request)
    {
        $request->validate([
            'nomor_plat' => 'required|string',
        ]);

        $mobil = Mobil::where('nomor_plat', $request->nomor_plat)->first();

        if (!$mobil) {
            return response()->json(['error' => 'mobil tidak ditemukan'], 404);
        }

        $rental = Rent::where('mobil_id', $mobil->_id)
                        ->where('user_id', $request->user()->_id)
                        ->whereNull('returned_at')
                        ->first();

        if (!$rental) {
            return response()->json(['error' => 'tidak ada mobil yang tersedia'], 404);
        }

        $rental->returned_at = Carbon::now();
        $rental->save();

        $daysRented = Carbon::parse($rental->start_date)->diffInDays($rental->end_date);
        $totalCost = $daysRented * $mobil->tarif_per_hari;

        return response()->json([
            'message' => 'Pengembalian mobil sukses',
            'total_cost' => $totalCost
        ]);
    }

    public function viewRent(Request $request)
{
    $user = $request->user();
    $userId = $user->id;
    $isAdmin = $user->role === 'admin';

    $rentalsQuery = Rent::with(['user', 'mobil']);

    if (!$isAdmin) {
        $rentalsQuery->where('user_id', $userId);
    }

    $rentals = $rentalsQuery->get();

    if ($rentals->isEmpty()) {
        return response()->json(['message' => 'Tidak ada rental untuk pengguna ini'], 404);
    }

    return response()->json($rentals->map(function ($rental) {
        return [
            'start_date' => $rental->start_date,
            'end_date' => $rental->end_date,
            'returned_at' => $rental->returned_at,
            'mobil' => [
                'merek' => $rental->mobil->merek,
                'model' => $rental->mobil->model,
                'nomor_plat' => $rental->mobil->nomor_plat,
                'tarif_per_hari' => $rental->mobil->tarif_per_hari,
            ],
            'user' => [
                'nama' => $rental->user->nama,
                'alamat' => $rental->user->alamat,
                'nomor_telepon' => $rental->user->nomor_telepon,
                'nomor_sim' => $rental->user->nomor_sim,
            ],
        ];
    }));
}


}
