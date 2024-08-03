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
        $query = Mobil::query();
        
        if ($request->has('merek')) {
            $query->where('merek', $request->merek);
        }

        if ($request->has('model')) {
            $query->where('model', $request->model);
        }

        $mobil = $query->get();
        return response()->json($mobil);
    }

    public function listMobil()
    {
        $mobil = Mobil::all();
        return response()->json($mobil);
    }
}
