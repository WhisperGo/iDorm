iDorm - Smart Dormitory Management System
iDorm adalah platform manajemen asrama modern yang dirancang untuk menyederhanakan koordinasi antara penghuni, pengelola (PIC Mahasiswa), dan administrasi pusat. Sistem ini fokus pada efisiensi penggunaan fasilitas asrama melalui algoritma anti-clash dan transparansi pelaporan keluhan gedung.

🚀 Fitur Utama
1. Sistem Otentikasi Khusus
4-Digit Card ID Access: Login menggunakan ID kartu 4-digit yang dipersonalisasi untuk penghuni.

Role-Based Access Control (RBAC): Pembagian akses spesifik untuk Resident, Manager (PIC), dan Superadmin.

2. Manajemen Fasilitas & Booking (Anti-Clash)
Real-time Schedule: Visualisasi jadwal peminjaman fasilitas asrama (Dapur, Mesin Cuci, Theater, CWS, dan Serba Guna).

Anti-Clash Engine: Algoritma validasi yang mencegah terjadinya bentrok jadwal pada jam dan item yang sama.

Early Release: Fitur bagi penghuni untuk menyelesaikan peminjaman lebih awal guna memberikan kesempatan bagi pengguna lain.

Cleanliness Evidence: Kewajiban unggah foto bukti kebersihan pasca penggunaan fasilitas.

3. Sistem Pelaporan Keluhan Gedung
Building Complaints: Form pelaporan kerusakan gedung yang terintegrasi dengan unggah foto bukti.

Status Tracking: Pemantauan status perbaikan secara real-time (Pending, On Progress, Resolved).

4. Keamanan & Moderasi
Facility-Based Suspend: Kemampuan Superadmin untuk membekukan akses pengguna pada fasilitas tertentu berdasarkan pelanggaran yang dilakukan.

🛠️ Teknologi yang Digunakan
Backend: Laravel 12.x (PHP 8.4+)

Database: MySQL

Frontend: Blade Templating Engine, CSS (Tailwind/Bootstrap components)

Integration: Fast API (untuk Chatbot)

💻 Cara Instalasi
Ikuti langkah-langkah berikut untuk menjalankan proyek ini di local server :

1. Clone Repository
git clone https://github.com/WhisperGo/iDorm.git
cd idorm

2. Instalasi Dependancy
composer install

3. Konfigurasi Environment
Salin file .env.example menjadi .env dan sesuaikan konfigurasi database.
cp .env.example .env
php artisan key:generate

4. Migrasi & Seeder Database
Jalankan migrasi untuk menyusun struktur tabel.
php artisan migrate --seed

5. Jalankan Aplikasi
php artisan serve