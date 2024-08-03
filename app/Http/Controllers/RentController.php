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
            'mobil_id' => 'required|exists:mobil_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $car = Rent::find($request->car_id);

        // verifikasi available mobil
        $rentals = Rent::where('mobil_id', $car->_id)
                        ->where(function($query) use ($request) {
                            $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
                        })
                        ->exists();

        if ($rentals) {
            return response()->json(['error' => 'Mobil tidak tersedia'], 400);
        }

        $rental = Rent::create([
            'user_id' => $request->user()->_id,
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
        $totalCost = $daysRented * $mobil->rental_rate_per_day;

        return response()->json([
            'message' => 'Pengembalian mobil sukses',
            'total_cost' => $totalCost
        ]);
    }

    public function viewRent(Request $request)
    {
        $rentals = Rent::where('user_id', $request->user()->_id)->get();
        return response()->json($rentals);
    }
}
