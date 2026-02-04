<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Kamar; // Asumsi kamu punya model Kamar untuk nampilin hasil

class PredictionController extends Controller
{
    public function index()
    {
        return view('penghuni.prediction.index');
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $validated = $request->validate([
            'region' => 'required',
            'harga' => 'required|numeric',
            'luas_kamar' => 'required|numeric',
            'tipe_kos' => 'required',
        ]);

        // 2. Simpan Histori Prediksi
        $prediction = Prediction::create([
            'user_id' => Auth::id(),
            'region' => $request->region,
            'harga' => $request->harga,
            'luas_kamar' => $request->luas_kamar,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'tipe_kos' => $request->tipe_kos,
            'is_km_dalam' => $request->has('is_km_dalam'),
            'is_water_heater' => $request->has('is_water_heater'),
            'is_furnished' => $request->has('is_furnished'),
            'is_listrik_free' => $request->has('is_listrik_free'),
            'is_parkir_mobil' => $request->has('is_parkir_mobil'),
            'is_mesin_cuci' => $request->has('is_mesin_cuci'),
        ]);

        // 3. Logika Mencari Kos yang Mirip (Dummy Logic)
        // Nanti di sini kamu bisa pasang algoritma benerannya
        $results = Kamar::where('status', 'available')
                    ->where('harga', '<=', $request->harga + 200000)
                    ->where('tipe_kos', $request->tipe_kos)
                    ->get();

        return view('penghuni.prediction.result', compact('results', 'prediction'));
    }
}