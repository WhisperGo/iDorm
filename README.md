<<<<<<< HEAD
# iDorm - Dormitory Management System

iDorm adalah sistem manajemen asrama terpadu yang dibangun menggunakan framework Laravel. Sistem ini dirancang untuk mendigitalisasi operasional asrama, mulai dari peminjaman fasilitas hingga pelaporan kerusakan gedung secara terpusat.

## 🚀 Fitur Utama

- **Sistem Otentikasi Khusus**: Login menggunakan **4-Digit Card ID** unik yang terintegrasi dengan database penghuni.
- **Engine Peminjaman Fasilitas (Anti-Clash)**:
    - Peminjaman fasilitas berbasis slot waktu 15 menit.
    - Logika *Anti-Clash* untuk mencegah tabrakan jadwal pemakaian item yang sama.
    - Fitur **Early Release** untuk mengoptimalkan ketersediaan alat bagi penghuni lain.
- **Building Maintenance Reporting**: Pelaporan kerusakan gedung oleh penghuni yang dilengkapi dengan fitur unggah foto bukti.
- **Targeted Facility Suspension**: Sistem pembekuan akses yang spesifik per fasilitas (misal: suspen mesin cuci saja) untuk menjaga ketertiban penggunaan alat.
- **Announcement Hub**: Pusat informasi digital untuk pengumuman resmi dari pengelola asrama.

## 🛠️ Stack Teknologi

- **Backend**: Laravel 12.x.
- **Database**: MySQL (Skema Relasional 3NF).
- **Frontend**: Blade Engine dengan integrasi *Standardized Component System*.
- **Keamanan**: Implementasi *Soft Deletes* pada aksi delete.
=======

>>>>>>> development
