<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf; // Import di paling atas

class PredictionController extends Controller
{
    /**
     * Menampilkan halaman form prediksi
     */
    public function index()
    {
        return view('penghuni.prediction.index'); // Pastikan folder & nama file blade-nya benar
    }

    /**
     * Mengirim data ke Python FastAPI dan menerima hasil prediksi
     */
    public function store(Request $request)
    {
        // dd("Tombol diklik, data masuk!", $request->all());
        // dd($request->all());
        // 1. VALIDASI INPUT
        // Pastikan semua field yang dibutuhkan model AI sudah terisi
        $request->validate([
            'region' => 'required',
            'tipe_kos' => 'required',
            // 'harga'       => 'required|numeric|min:100000', // dari input hidden hargaMurni
            'luas_kamar' => 'required|numeric|min:1',
        ], [
            'region.required' => 'Waduh, wilayahnya pilih dulu dong!',
            // 'harga.required'  => 'Harga penawarannya jangan dikosongin ya.',
            'harga.numeric' => 'Harganya harus angka, jangan pakai perasaan.',
        ]);

        // 2. MAPPING DATA (Laravel -> Python)
        // Kita sesuaikan bahasa dari UI Laravel ke "bahasa" yang dimengerti Python
        $tipeMap = [
            'cowo' => 'putra',
            'cewe' => 'putri',
            'campur' => 'campur'
        ];

        // Konversi region (Contoh: "Jakarta Pusat" -> "jakarta_pusat")
        $regionInput = str_replace(' ', '_', strtolower($request->region));

        try {
            // 3. TEMBAK API FASTAPI
            // Kita bungkus dengan timeout biar kalau Python-nya mati, Laravel nggak nunggu selamanya
            $response = Http::post("http://127.0.0.1:8002/predict/{$regionInput}", [
                // 'region'          => $regionInput,
                'luas_kamar' => (float) $request->luas_kamar,
                'jarak_ke_bca' => (float) ($request->jarak_ke_bca ?? 0.5), // Contoh default 0.5km
                'tipe_kos' => $tipeMap[$request->tipe_kos] ?? 'campur',
                'latitude' => (float) $request->latitude,
                'longitude' => (float) $request->longitude,
                'harga_tawaran' => (float) $request->harga,
                'is_km_dalam' => $request->has('is_km_dalam') ? 1 : 0,
                'is_water_heater' => $request->has('is_water_heater') ? 1 : 0,
                'is_furnished' => $request->has('is_furnished') ? 1 : 0,
                'is_listrik_free' => $request->has('is_listrik_free') ? 1 : 0,
                'is_parkir_mobil' => $request->has('is_parkir_mobil') ? 1 : 0,
                'is_mesin_cuci' => $request->has('is_mesin_cuci') ? 1 : 0,
            ]);

            // 4. HANDLING RESPONSE
            if ($response->successful()) {
                // dd($result = $response->json());
                $data = $response->json();

                // Base price from MLflow
                $basePrice = $data['predicted_price'] ?? 0;
                
                // Define true MAE (Mean Absolute Error) ranges based on the notebook training results
                $maeMargins = [
                    'jakarta_pusat' => 538324,
                    'jakarta_selatan' => 500346,
                    'jakarta_utara' => 256533,
                    'yogyakarta' => 255535
                ];
                
                // Assign MAE or fallback to 300000
                $margin = $maeMargins[$regionInput] ?? 300000;
                
                $offeredPrice = (float) $request->harga;
                $minPrice = $basePrice - $margin;
                $maxPrice = $basePrice + $margin;

                // Hitung Verdict Wajar/Overprice/Underprice
                if ($offeredPrice < $minPrice) {
                    $verdict = 'Underprice';
                    $color_code = 'warning';
                    $description = 'Harga ini sangat murah di bawah standar pasar. Pastikan tidak ada masalah tersembunyi dengan properti.';
                } elseif ($offeredPrice > $maxPrice) {
                    $verdict = 'Overprice';
                    $color_code = 'danger';
                    $description = 'Harga ini tergolong mahal di atas standar pasar rata-rata. Periksa kembali kelayakan fasilitas yang ditawarkan.';
                } else {
                    $verdict = 'Wajar';
                    $color_code = 'success';
                    $description = 'Harga penawaran ini tergolong standar/wajar di pasar saat ini.';
                }

                // KUNCI PERBAIKAN: Kita bungkus ulang datanya agar sesuai dengan variabel di Blade kamu
                $formattedRes = [
                    'status' => 'success',
                    'metadata' => [
                        'region' => $data['region'] ?? $request->region,
                        'mae_margin' => $data['metadata']['mae_margin'] ?? $margin,
                        'calculated_distance' => $data['metadata']['calculated_distance'] ?? 0,
                    ],
                    'result' => [
                        'base_prediction' => $basePrice, // Python ngirim ini, Blade nyari base_prediction
                        'fair_range' => $data['fair_range'] ?? [
                            'min' => $minPrice,
                            'max' => $maxPrice
                        ],      // Isinya min & max
                        'offered_price' => $offeredPrice,   // Ambil dari input awal user
                        'analysis' => $data['analysis'] ?? [
                            'verdict' => $verdict,
                            'color_code' => $color_code,
                            'description' => $description
                        ],        // Isinya verdict, color_code, description
                    ]
                ];

                // Simpan ke session buat fitur PDF & Refresh
                session(['prediction_data' => $formattedRes]);

                // Kirim ke Blade dengan nama variabel 'res'
                return redirect()->route('prediction.result');
            }

            return back()->with('error', 'Gagal memproses prediksi.')->withInput();

        } catch (\Exception $e) {
            // Jika server Python mati/koneksi gagal
            Log::error("Koneksi FastAPI Gagal: " . $e->getMessage());
            return back()->with('error', 'Gagal terhubung ke AI Service. Pastikan server Python sudah jalan di port 8001 ya!')->withInput();
        }
    }

    // FUNGSI BARU UNTUK MENAMPILKAN VIEW HASIL
    public function showResult()
    {
        $res = session('prediction_data');

        // Jika user akses langsung tanpa data (misal refresh), balikin ke form
        if (!$res) {
            return redirect()->route('prediction.index');
        }

        return view('feature.prediction_result', compact('res'));
    }

    public function downloadPdf()
    {
        $res = session('prediction_data');

        if (!$res) {
            return redirect()->route('prediction.index');
        }

        // Load view khusus PDF dan set kertas A4
        $pdf = Pdf::loadView('feature.pdf_result', compact('res'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Analisis-iDorm-' . now()->format('dmyHis') . '.pdf');
    }
}