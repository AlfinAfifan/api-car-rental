<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use Illuminate\Http\Request;

class MobilController extends Controller
{
    public function addMobil(Request $request)
    {
        // validate input mobil info
        $request->validate([
            'merek' => 'required|string',
            'model' => 'required|string',
            'nomor_plat' => 'required|string',
            'tarif_per_hari' => 'required|numeric',
        ]);

        $mobil = Mobil::create($request->all());
        return response()->json($mobil, 201);
    }

    public function searchMobil(Request $request)
{
    // Check if any search parameters are provided
    if (!$request->has('merek')) {
        return response()->json(['message' => 'Mobil tidak ditemukan'], 404);
    }

    $query = Mobil::query();
    
    // Add a filter if the 'merek' parameter is present.
    if ($request->has('merek')) {
        $query->where('merek', $request->merek);
    }

    // Get query result
    $mobil = $query->get();

    // If query result empty
    if ($mobil->isEmpty()) {
        return response()->json(['message' => 'Mobil tidak ditemukan'], 404);
    }

    // If query success
    return response()->json($mobil);
}


    public function listMobil()
    {
        $mobil = Mobil::all();
        return response()->json($mobil);
    }
}
