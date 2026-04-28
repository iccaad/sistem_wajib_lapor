# Sistem Wajib Lapor Digital

Sistem Wajib Lapor Digital adalah aplikasi berbasis web yang dibangun menggunakan **Laravel 13** untuk mendigitalisasi proses wajib lapor bagi peserta pengawasan (seperti tahanan kota, wajib lapor lantas, dll) di lingkungan kepolisian (Polrestabes). Sistem ini menggantikan proses lapor manual berbasis kertas menjadi sistem digital berbasis lokasi (GPS) dan verifikasi biometrik (Selfie).

Aplikasi ini dibagi menjadi dua portal utama:
1. **Portal Admin**: Untuk petugas kepolisian mengelola data peserta, lokasi wajib lapor, memantau tingkat kepatuhan, dan mencetak laporan bulanan.
2. **Portal Peserta**: Untuk peserta wajib lapor melakukan presensi mandiri menggunakan smartphone mereka melalui deteksi GPS dan kamera selfie.

---

## 🎯 Fitur Utama

### 1. Portal Peserta (Wajib Lapor)
- **Login Berbasis NIK**: Peserta tidak perlu mengingat password rumit, cukup menggunakan NIK (Nomor Induk Kependudukan) mereka. Terdapat *Rate Limiter* untuk mencegah brute-force NIK.
- **Deteksi Lokasi Akurat (GPS + Haversine)**: Aplikasi memverifikasi posisi GPS perangkat peserta secara real-time. Radius lokasi (misal 50 meter) divalidasi baik di sisi klien (*client-side*) menggunakan Javascript maupun di sisi server (*server-side*).
- **Penugasan Lokasi Sekuensial**: Peserta diberikan rute/lokasi spesifik untuk setiap absennya secara berurutan. Peta akan otomatis menyoroti dan mengarahkan peserta ke *Next Required Location* (Lokasi Wajib Lapor Berikutnya).
- **Verifikasi Selfie**: Integrasi kamera perangkat secara langsung untuk mengambil foto selfie sebagai bukti kehadiran.
- **Dashboard Progress**: Menampilkan indikator persentase kehadiran, sisa kuota lapor per periode (mingguan/bulanan), dan masa tenggang pengawasan.
- **Sistem Peringatan Terpusat**: Pemberitahuan otomatis (Level 1, Level 2, Level 3) jika peserta mulai mangkir dari jadwal.

### 2. Portal Admin (Petugas)
- **Manajemen Peserta**: CRUD data peserta, penentuan masa pengawasan, pemilihan jenis pelanggaran, serta penetapan kuota wajib lapor.
- **Distribusi Lokasi Lapor**: Admin menentukan lokasi mana saja yang harus dikunjungi oleh peserta sesuai kuota secara berurutan.
- **Manajemen Lokasi (Geofencing)**: Penambahan titik koordinat lokasi wajib lapor beserta penyesuaian radius toleransi (dalam meter) yang terintegrasi dengan Leaflet.js.
- **Validasi Kehadiran & Override Manual**: Melihat bukti foto selfie dan lokasi presensi peserta. Admin juga memiliki otoritas *Manual Override* jika terjadi kendala teknis (misal perangkat peserta rusak).
- **Sistem *Log Activity***: Semua tindakan krusial admin (Create, Update, Delete) akan direkam otomatis oleh `LogActivityMiddleware` untuk tujuan audit.
- **Pelaporan & Ekspor**: Rekapitulasi absensi peserta untuk kebutuhan pemberkasan.

### 3. Otomatisasi (Task Scheduler)
- **Generate Periode Otomatis**: Secara otomatis memotong atau memperpanjang periode wajib lapor (Mingguan/Bulanan) tepat pada jam 00:05 WIB.
- **Peringatan Mangkir**: *Cron job* berjalan setiap hari untuk mendeteksi peserta yang gagal memenuhi target kuota dan secara otomatis menerbitkan peringatan (Warning L1/L2/L3) serta mengirim notifikasi Email.

---

## 🛠️ Stack Teknologi

- **Framework**: Laravel 13.x (PHP 8.2+)
- **Database**: PostgreSQL (direkomendasikan) / MySQL
- **Frontend**: Blade Templating, Tailwind CSS (Utility-First), Alpine.js (Lightweight Javascript behavior)
- **Peta & Geolocation**: Leaflet.js (OpenStreetMap)
- **Authentication**: Laravel Breeze (Admin) & Custom Auth (Peserta NIK)

---

## 🚀 Panduan Instalasi (Development)

Berikut adalah langkah-langkah untuk menjalankan aplikasi secara lokal.

### 1. Kloning Repositori
```bash
git clone https://github.com/iccaad/sistem_wajib_lapor.git
cd sistem_wajib_lapor
```

### 2. Instalasi Dependensi
Pastikan [Composer](https://getcomposer.org/) dan [Node.js/NPM](https://nodejs.org/) sudah terinstal.
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
Buat file `.env` dari file `.env.example`.
```bash
cp .env.example .env
```
Generate APP_KEY Laravel:
```bash
php artisan key:generate
```

Ubah konfigurasi database di file `.env` sesuai dengan database lokal Anda:
```env
DB_CONNECTION=pgsql # atau mysql
DB_HOST=127.0.0.1
DB_PORT=5432        # 3306 untuk mysql
DB_DATABASE=sistem_wajib_lapor
DB_USERNAME=root
DB_PASSWORD=
```
> **Catatan**: Aplikasi ini dikonfigurasi untuk bahasa Indonesia. Pastikan `APP_LOCALE=id` dan `APP_FAKER_LOCALE=id_ID` ada di file `.env`.

### 4. Migrasi & Seeder Database
Aplikasi dilengkapi dengan dummy data yang komprehensif (termasuk admin, peserta, lokasi, riwayat absen, dsb).
```bash
php artisan migrate:fresh --seed
```

### 5. Storage Link
Buat symlink agar foto selfie dan file lokal bisa diakses (meskipun foto absensi disimpan secara *private*).
```bash
php artisan storage:link
```

### 6. Jalankan Server
Jalankan Vite untuk kompilasi *asset* (Tailwind/Alpine) dan PHP *development server*. Buka dua terminal terpisah:

**Terminal 1:**
```bash
npm run dev
```

**Terminal 2:**
```bash
php artisan serve
```

---

## 🔐 Akses Pengguna Default

Setelah *seeder* berhasil dijalankan, Anda bisa login menggunakan akun berikut:

**Portal Admin** (Akses di: `http://localhost:8000/admin/login`)
- Email: `budi.santoso@polrestabes-smg.test` (atau admin lain yang ada di Database)
- Password: `password`

**Portal Peserta** (Akses di: `http://localhost:8000/login`)
- NIK: `1234567890123451` s/d `1234567890123460` (Bisa dicek melalui tabel `participants` atau di halaman admin).

---

## ⏰ Cron Job (Scheduler)

Sistem ini sangat bergantung pada otomatisasi periode dan pengecekan tingkat kepatuhan absensi. Untuk lingkungan *development*, Anda bisa menjalankan scheduler secara manual atau biarkan proses `schedule:work` berjalan.

Jalankan perintah ini di **Terminal 3**:
```bash
php artisan schedule:work
```
*(Ini akan menjalankan Task Scheduler setiap menit untuk mensimulasikan Cron)*.

---

## 🛡️ Keamanan (Security Features)

1. **Private File System**: Foto selfie diunggah ke `storage/app/private`. Foto disajikan menggunakan *controller* melalui `BinaryFileResponse` sehingga tidak terekspos ke publik dan hanya Admin terautentikasi yang bisa melihatnya.
2. **Rate Limiting**: Peserta yang gagal login menggunakan NIK 5x dalam 10 menit akan diblokir sementara (*IP Based*). Rute pengiriman absensi (POST) juga dibatasi maksimal 10x sehari per user untuk mencegah SPAM/Auto-clicker.
3. **Session Timeout**: Diatur ke 30 menit (idle) melalui konfigurasi `SESSION_LIFETIME`.

---

## 📝 Lisensi
Sistem ini dibangun untuk kepentingan instansi terkait dan dikembangkan secara *in-house*. Tidak untuk diperjualbelikan secara komersial tanpa izin.
