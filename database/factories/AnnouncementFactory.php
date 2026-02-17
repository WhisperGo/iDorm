<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Kumpulan kalimat pengumuman khas asrama Indonesia
        $isi_pengumuman = [
            'Mohon perhatian kepada seluruh penghuni asrama untuk menjaga kebersihan koridor masing-masing.',
            'Diberitahukan bahwa akan ada pemadaman listrik bergilir untuk pemeliharaan genset pada akhir pekan ini.',
            'Bagi yang merasa kehilangan kunci motor di parkiran, silakan mengambilnya di ruang pengelola dengan membawa bukti.',
            'Dilarang membawa tamu lawan jenis masuk ke dalam area kamar asrama sesuai dengan peraturan yang berlaku.',
            'Harap segera melaporkan jika ada fasilitas kamar yang rusak ke bagian teknisi melalui aplikasi ini.',
            'Jadwal piket bersama untuk area dapur umum telah diperbarui, mohon dicek di papan pengumuman lobi.',
            'Pastikan mencabut stop kontak dan mematikan lampu sebelum meninggalkan kamar asrama.',
            'Iuran bulanan asrama paling lambat dibayarkan pada tanggal 10 setiap bulannya, terima kasih.',
            'Akan diadakan kegiatan makan bersama dalam rangka syukuran renovasi gedung pada Sabtu malam.',
            'Mohon tidak meletakkan sampah plastik di dalam bak sampah organik yang telah disediakan.'
        ];
    
        return [
            'author_id' => 1,
            'title' => $this->faker->randomElement([
                'Pemberitahuan: Jadwal Piket',
                'Penting: Kebersihan Kamar',
                'Info: Pemeliharaan Fasilitas',
                'Update: Jam Malam Asrama',
                'Peringatan: Keamanan Parkir'
            ]),
            // Kita gabungkan beberapa kalimat acak dari array di atas
            'content' => $this->faker->randomElement($isi_pengumuman) . ' ' . $this->faker->randomElement($isi_pengumuman) . ' ' . $this->faker->randomElement($isi_pengumuman),
        ];
    }
}
