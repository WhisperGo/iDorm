<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    private function handleDirectBooking()
    {
        // Ambil data dari session yang disimpan saat 'check_availability' tadi
        $entities = session('pending_booking');

        if (!$entities) {
            return response()->json([
                'status' => 'success',
                'data' => ['bot_reply' => "I'm sorry, what would you like to book? Please specify the room and time first."]
            ]);
        }

        $roomName = strtolower($entities['room']);

        // --- DI SINI TEMPATNYA ---
        // Cek apakah ruangan butuh input tambahan (alat/unit)
        if ($roomName === 'dapur' || $roomName === 'sergun' || $roomName === 'kitchen') {
            return response()->json([
                'status' => 'success',
                'data' => [
                    // Kita suruh user klik tombol hijau yang tadi sudah muncul di chat
                    'bot_reply' => "Since you're booking the **" . $entities['room'] . "**, you need to select the cooking tools or specific units. Please use the **Confirm Booking** button above to finish!",
                    'intent' => 'redirect_to_tools'
                ]
            ]);
        }

        // JIKA BUKAN DAPUR/SERGUN (Misal: Theater atau CWS), LANGSUNG TULIS KE DB
        try {
            $facility = \App\Models\Facility::where('name', 'LIKE', "%$roomName%")->first();
            $item = \App\Models\FacilityItem::where('facility_id', $facility->id)->first();

            \App\Models\Booking::create([
                'user_id' => Auth::id(),
                'facility_id' => $facility->id,
                'facility_item_id' => $item->id,
                'booking_date' => $entities['date'],
                'start_time' => str_replace('.', ':', $entities['start_time']),
                'end_time' => str_replace('.', ':', $entities['end_time'] ?? '21:00'),
                'status_id' => 1, // Pending
                'cleanliness_status' => 'pending',
            ]);

            session()->forget('pending_booking'); // Hapus ingatan setelah sukses

            return response()->json([
                'status' => 'success',
                'data' => [
                    'bot_reply' => "Great! I've placed your booking for **" . $facility->name . "**. You can see it in your dashboard!",
                    'intent' => 'booking_confirmed'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => ['bot_reply' => "Sorry, I couldn't save your booking. Please try manually."]
            ]);
        }
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);
        $userMsg = strtolower(trim($request->message));

        // --- TRICK: Cek manual untuk konfirmasi (Tanpa tanya Python) ---
        $confirmWords = ['yes', 'oke', 'boleh', 'siap', 'book it', 'pasti'];

        if (in_array($userMsg, $confirmWords) && session()->has('pending_booking')) {
            return $this->handleDirectBooking();
        }

        try {
            // 1. Tembak ke server Python port 8001
            $response = Http::post('http://127.0.0.1:8001/predict', [
                'message' => $request->message,
            ]);

            if (!$response->successful()) {
                throw new \Exception("FastAPI Error");
            }

            $result = $response->json();
            $intent = $result['data']['intent'] ?? 'unknown';
            $entities = $result['data']['entities'] ?? [];

            // 2. LOGIKA JIKA USER MAU CEK KETERSEDIAAN
            if ($intent === 'check_availability') {
                return $this->handleCheckAvailability($result, $entities);
            }

            // Tambahkan di dalam method sendMessage
            if ($intent === 'confirm_booking' || ($intent === 'affirm' && session('pending_booking'))) {
                return $this->handleDirectBooking();
            }

            // Simpan context ketersediaan di session jika tersedia
            if ($intent === 'check_availability' && $result['data']['available']) {
                session(['pending_booking' => $entities]);
            }

            // 3. JIKA BUKAN CEK JADWAL, LANGSUNG BALIKIN RESPON ASLI (booking_request, dll)
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error("Chatbot Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'data' => ['bot_reply' => 'Maaf, AI sedang mengalami gangguan koneksi internal.']
            ], 500);
        }
    }

    private function handleCheckAvailability($originalResult, $entities)
    {
        // 1. Ambil entity dan bersihkan spasi
        $roomEntity = isset($entities['room']) ? trim($entities['room']) : null;
        $date = $entities['date'] ?? null;
        $start = $entities['start_time'] ?? null;

        if (!$roomEntity) {
            $originalResult['data']['bot_reply'] = "Which room would you like to check?";
            return response()->json($originalResult);
        }

        // 2. MAPPING LAYER (Penerjemah AI ke Nama DB)
        // Pastikan kunci di sini menggunakan huruf KECIL semua
        $searchMap = [
            'sergun'     => 'Serba',
            'serba guna' => 'Serba',
            'dapur'      => 'Dapur',
            'kitchen'    => 'Dapur',
            'cws'        => 'Co-Working',
            'theater'    => 'Theater',
            'laundry'    => 'Mesin Cuci',
            'mesin cuci' => 'Mesin Cuci'
        ];

        // Ambil kata kunci berdasarkan mapping, default ke nama asli jika tidak ada di map
        $searchTerm = $searchMap[strtolower($roomEntity)] ?? $roomEntity;

        // 3. CARI DI DATABASE (Gunakan LIKE yang lebih fleksibel)
        // Kita cari yang namanya mengandung $searchTerm ATAU mengandung $roomEntity asli
        $facility = \App\Models\Facility::where('name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('name', 'LIKE', '%' . $roomEntity . '%')
                    ->first();

        // DEBUGGING: Jika masih gagal, kita kirim pesan error yang sangat detail
        if (!$facility) {
            $originalResult['data']['bot_reply'] = "System Error: I couldn't find any room matching '$roomEntity' or '$searchTerm'. Please check your Facility table in Database.";
            return response()->json($originalResult);
        }

        // 4. VALIDASI TANGGAL & JAM (Prompting)
        if (!$date || !$start) {
            $originalResult['data']['bot_reply'] = "I found the " . $facility->name . ", but I need a specific date and time to check the schedule.";
            return response()->json($originalResult);
        }

        // 5. FORMAT WAKTU & CEK BOOKING (Overlap Logic)
        $startTime = str_replace('.', ':', $start);
        if (strlen($startTime) == 5) $startTime .= ':00';

        // Default 2 jam durasi
        $endTime = date('H:i:s', strtotime($startTime . ' + 2 hours'));

        $isBooked = \App\Models\Booking::where('facility_id', $facility->id)
            ->where('booking_date', $date)
            ->whereIn('status_id', [1, 2, 4]) // Pending, Approved, Active
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })
            ->exists();

        // 6. RESPON AKHIR
        if ($isBooked) {
            $originalResult['data']['bot_reply'] = "I've checked the schedule. Unfortunately, " . $facility->name . " is already booked on $date at $start.";
            $originalResult['data']['available'] = false;
        } else {
            $originalResult['data']['bot_reply'] = "Great news! " . $facility->name . " is available on $date at $start. You can book it now!";
            $originalResult['data']['available'] = true;
        }

        $originalResult['data']['intent'] = 'check_availability';
        $originalResult['data']['entities'] = $entities; 

        return response()->json($originalResult);
    }
}