# Konsep Sistem Informasi Wajib Lapor Digital
### Rancang Bangun Berbasis Web | Laravel 13 · Breeze · PostgreSQL · Geolocation

---

## 1. Gambaran Umum

Sistem web untuk membantu petugas Polres dalam mengelola, memantau, dan mendokumentasikan proses wajib lapor peserta pembinaan. Menggantikan pencatatan manual yang rawan manipulasi, data tercecer, dan sulit dimonitor secara konsisten.

**Cakupan pelanggaran yang ditangani:**
- Balap liar
- Tawuran
- Gangguan ketertiban umum
- Pelanggaran sosial tertentu
- Kenakalan remaja via pembinaan terarah

---

## 2. Teknologi yang Digunakan

| Layer | Teknologi | Versi | Alasan |
|---|---|---|---|
| Backend | Laravel | 13.x | Mature, secure, Eloquent ORM, Scheduler |
| Auth Scaffolding | Laravel Breeze | Latest | Setup auth admin cepat, tidak perlu bangun dari nol |
| Frontend | Blade + Alpine.js | - | Ringan, tidak butuh SPA, integrasi natural dengan Breeze |
| Styling | Tailwind CSS | v3 | Sudah termasuk dalam Breeze stack Blade |
| Database | PostgreSQL | 16.x | Stabil, relasional kuat, cocok data koordinat |
| DB Client Lokal | Laragon | - | PostgreSQL bawaan Laragon, akses via port 5432 |
| Peta | Leaflet.js + OpenStreetMap | - | Open source, cukup untuk visualisasi lokasi & radius |
| Geolocation | Browser Geolocation API | - | Ambil koordinat GPS peserta saat absensi |
| Kalkulasi Jarak | Haversine Formula | - | Validasi jarak peserta ke lokasi resmi di server |
| Grafik | Chart.js | via CDN | Grafik kepatuhan di dashboard admin |
| Email | Laravel Mail + SMTP | - | Notifikasi peringatan Level 2 & 3 |
| Storage | Laravel Storage (local disk) | - | Simpan foto selfie peserta saat absensi |

**Tidak digunakan:** Vue/React, QR Code, Face Recognition, Mobile App native, package auth selain Breeze.

---

## 3. Arsitektur Login — Dua Domain Terpisah

Sistem ini memiliki **dua halaman login yang sepenuhnya terpisah**, dengan mekanisme autentikasi yang berbeda untuk setiap role.

```
/login          → Halaman login PESERTA  (NIK saja)
/admin/login    → Halaman login ADMIN    (Email + Password via Breeze)
```

Dua halaman ini tidak saling terhubung secara UI. Peserta tidak tahu ada `/admin/login`, dan admin tidak menggunakan `/login`.

---

## 4. Aktor & Mekanisme Login

### A. Admin (Petugas Polres)
**URL Login:** `/admin/login`
**Metode:** Email + Password (dikelola oleh Laravel Breeze)

Breeze menangani seluruh flow auth admin:
- Login form (email + password)
- Session management
- Redirect after login → `/admin/dashboard`
- Logout → `/admin/logout`
- Breeze juga generate fitur reset password, namun tidak wajib dipakai di v1

**Hak akses Admin:**
- Buat dan kelola data peserta (nama, NIK, masa pengawasan, kuota)
- Set password awal peserta tidak diperlukan (peserta login tanpa password)
- Kelola lokasi resmi wajib lapor (koordinat + radius)
- **Tentukan lokasi wajib lapor per peserta** (jumlah lokasi = kuota absensi)
- Lihat dan verifikasi riwayat absensi beserta foto selfie peserta
- Override manual absensi jika ada kondisi khusus (wajib isi alasan)
- Lihat status kepatuhan dan peringatan seluruh peserta
- Cetak laporan kepatuhan

### B. Peserta Wajib Lapor
**URL Login:** `/login`
**Metode:** NIK saja (tanpa password)

Peserta cukup mengetik 16 digit NIK mereka → sistem mencari record di database → jika ditemukan dan aktif, langsung masuk ke dashboard peserta. Tidak ada password sama sekali.

**Alasan desain ini diterima:**
- Peserta umumnya warga biasa yang tidak melek teknologi
- NIK sudah dipegang peserta dan tidak mungkin lupa
- Sistem ini bukan menyimpan data sensitif finansial — hanya pencatatan kehadiran
- Peserta diregistrasi secara fisik oleh admin, bukan self-register
- Akun peserta hanya bisa dinonaktifkan oleh admin jika ada masalah

**Hak akses Peserta:**
- Lihat status kewajiban dan riwayat kehadiran
- Lakukan absensi via GPS + upload foto selfie
- Lihat lokasi wajib lapor di peta
- Lihat peringatan aktif

---

## 5. Halaman Login — Detail Tampilan

### Halaman Login Peserta — `/login`

```
┌──────────────────────────────────────┐
│                                      │
│         SISTEM WAJIB LAPOR           │
│          POLRES [NAMA KOTA]          │
│                                      │
│   Masukkan NIK untuk melanjutkan     │
│                                      │
│   NIK (16 digit)                     │
│   ┌──────────────────────────────┐   │
│   │ Contoh: 3374010101990001     │   │
│   └──────────────────────────────┘   │
│                                      │
│   [ Masuk ke Sistem Wajib Lapor ]    │
│                                      │
│   Jika NIK tidak terdaftar,          │
│   hubungi petugas Polres.            │
│                                      │
└──────────────────────────────────────┘
```

- Hanya satu field: NIK (type text, maxlength 16, pattern angka saja)
- Tidak ada link ke halaman admin login
- Tidak ada link "lupa password" (tidak ada password)
- Pesan error jika NIK tidak ditemukan: *"NIK tidak terdaftar. Hubungi petugas Polres."*
- Pesan error jika akun dinonaktifkan: *"Akun Anda dinonaktifkan. Hubungi petugas Polres."*

### Halaman Login Admin — `/admin/login`

Menggunakan tampilan yang di-generate Breeze, dikustomisasi sedikit:

```
┌──────────────────────────────────────┐
│                                      │
│         SISTEM WAJIB LAPOR           │
│         Panel Admin Polres           │
│                                      │
│   Email                              │
│   ┌──────────────────────────────┐   │
│   │                              │   │
│   └──────────────────────────────┘   │
│                                      │
│   Password                           │
│   ┌──────────────────────────────┐   │
│   │                              │   │
│   └──────────────────────────────┘   │
│                                      │
│   [ Login ]                          │
│                                      │
└──────────────────────────────────────┘
```

- Breeze handle semuanya: validasi, session, redirect
- Redirect setelah login mengarah ke `/admin/dashboard` (dikonfigurasi di Breeze)
- Jika admin sudah login dan buka `/admin/login` → redirect ke `/admin/dashboard`

---

## 6. Konsep Kuota Kehadiran

Sistem menggunakan model **kuota per periode**, bukan jadwal hari tetap.

### Definisi Periode

Periode dihitung dari **tanggal mulai pengawasan peserta**, bukan kalender standar. Ini penting agar setiap peserta punya siklus sendiri sesuai tanggal mulai mereka.

**Contoh sistem mingguan (2x/minggu):**
- Peserta mulai pengawasan: 5 Januari
- Minggu 1: 5 Jan – 11 Jan → wajib hadir 2x
- Minggu 2: 12 Jan – 18 Jan → wajib hadir 2x
- dst. sampai masa pengawasan selesai

**Contoh sistem bulanan (6x/bulan):**
- Peserta mulai pengawasan: 5 Januari
- Bulan 1: 5 Jan – 4 Feb → wajib hadir 6x
- Bulan 2: 5 Feb – 4 Mar → wajib hadir 6x
- dst.

Setiap periode disimpan sebagai record eksplisit di tabel `attendance_periods` dengan kolom `period_start` dan `period_end`. **Tidak ada kalkulasi dinamis saat query** — ini mencegah bug logika kuota.

**Generate periode:**
- Periode pertama di-generate otomatis saat admin menyimpan data peserta baru
- Periode berikutnya di-generate otomatis oleh Scheduler setiap malam setelah periode aktif berakhir

### Aturan Absensi
- Maksimal **1 kali absensi per hari** per peserta
- Peserta bebas memilih hari hadir selama masih dalam rentang periode aktif
- Kuota dihitung dari jumlah record hadir dalam `period_start` s.d. `period_end`
- Jika masa pengawasan berakhir di tengah periode, periode berjalan hingga `supervision_end`

---

## 7. Mekanisme Absensi

### Alur Lengkap

```
Peserta buka /login → input NIK → masuk dashboard
         ↓
Klik tombol "Absensi Sekarang"
         ↓
Halaman absensi terbuka
         ↓
Klik "Deteksi Lokasi Saya"
→ Browser meminta izin GPS
→ Koordinat (lat, lng, accuracy) diperoleh
         ↓
Ambil foto selfie (langsung dari kamera device)
         ↓
Klik Submit
         ↓
Server menjalankan 8 validasi berurutan
         ↓
Gagal salah satu → pesan error spesifik + percobaan tercatat di DB
Lolos semua → absensi tersimpan + redirect dashboard dengan pesan sukses
```

### 8 Validasi Server-Side (Berurutan)

```
[1] Masa pengawasan aktif
    → today >= supervision_start DAN today <= supervision_end
    → Error: "Masa pengawasan Anda sudah berakhir."

[2] Belum absen hari ini
    → Tidak ada record di attendance_logs dengan attended_at = today
    → Error: "Anda sudah melakukan absensi hari ini."

[3] Kuota periode belum penuh
    → Jumlah hadir di periode aktif < quota_target
    → Error: "Target kehadiran periode ini sudah terpenuhi."

[4] Batas percobaan harian belum terlampaui
    → Jumlah record di attendance_attempts hari ini < 10
    → Error: "Terlalu banyak percobaan. Coba lagi besok atau hubungi petugas."

[5] Koordinat GPS diterima
    → latitude dan longitude tidak null/kosong
    → Error: "Gagal mendapatkan lokasi GPS. Pastikan GPS aktif dan izin lokasi diberikan."

[6] Akurasi GPS cukup
    → accuracy <= 500 meter
    → Error: "Sinyal GPS terlalu lemah. Pindah ke area dengan sinyal lebih baik."

[7] Berada dalam radius lokasi yang ditetapkan
    → Haversine distance ke salah satu lokasi YANG DITETAPKAN untuk peserta ini <= radius_meters
    → Error: "Anda berada di luar area lokasi wajib lapor yang ditetapkan."
    → Catatan: Peserta hanya bisa absen di lokasi yang ditetapkan admin, bukan di semua lokasi aktif

[8] Foto selfie valid
    → File ada, format JPEG/PNG, ukuran <= 5MB
    → Error: "Foto tidak valid. Gunakan kamera untuk mengambil foto."
```

Jika validasi 1–7 gagal, percobaan dicatat ke `attendance_attempts` beserta alasan penolakan dan koordinat yang dikirim. Validasi 8 jika gagal tidak disimpan ke attempts (foto belum ada).

### Validasi Jarak — Haversine Formula

Server menghitung jarak antara koordinat peserta dan setiap lokasi **yang ditetapkan untuk peserta tersebut** (bukan semua lokasi aktif). Jika peserta berada dalam radius **salah satu** lokasi yang ditetapkan, absensi diterima.

```
Lokasi: Sasana Olahraga, radius 100 meter

Peserta di 45m  → VALID ✓
Peserta di 99m  → VALID ✓
Peserta di 101m → DITOLAK ✗

Sistem cek lokasi berikutnya → Balai Pembinaan, radius 150m
Peserta di 120m dari sini → VALID ✓
```

### Foto Selfie

- Input file dengan atribut `capture="camera"` → tidak bisa pilih dari galeri, harus langsung dari kamera
- Timestamp dicatat oleh server saat file diterima (tidak mengandalkan metadata EXIF device)
- Disimpan di `storage/app/private/selfies/{participant_id}/{tanggal}/` → tidak bisa diakses dari URL publik
- Admin bisa melihat foto selfie via route yang dilindungi middleware, bukan direct file access
- Format: JPEG atau PNG, maksimal 5MB

### Mitigasi GPS Spoofing

Untuk skala Polres dengan peserta umumnya tidak tech-savvy, ini sudah cukup tanpa hardware tambahan:

| Mitigasi | Cara Kerja |
|---|---|
| Validasi akurasi | Tolak jika `accuracy` > 500m — biasanya indikasi GPS mock atau sinyal sangat buruk |
| Batas percobaan harian | Maksimal 10 percobaan per hari per peserta |
| Cooldown 2 menit | Setelah percobaan ditolak karena lokasi, harus tunggu 2 menit sebelum bisa coba lagi (dikontrol Alpine.js di frontend) |
| Log seluruh percobaan | Koordinat setiap percobaan (berhasil & ditolak) tersimpan — admin bisa audit jika ada pola mencurigakan |
| Foto selfie | Bukti visual yang diambil langsung dari kamera — bisa dicek admin jika ada kecurigaan |

### Override Manual oleh Admin

Untuk kondisi khusus (sinyal GPS buruk di lokasi, device peserta error, dll):
- Admin input absensi manual dari halaman detail peserta
- Field alasan **wajib diisi** — tidak bisa dikosongkan
- Tersimpan di `attendance_logs` dengan `is_manual_override = true`
- Tercatat di `activity_logs` dengan ID admin + timestamp
- Di riwayat peserta, entri ini ditandai badge **"Input Manual"** berwarna berbeda

---

## 8. Struktur Database — Detail Lengkap

### Tabel: users

```
id                  BIGSERIAL PRIMARY KEY
name                VARCHAR(255) NOT NULL
email               VARCHAR(255) UNIQUE NULLABLE       -- admin saja
nik                 CHAR(16) UNIQUE NULLABLE            -- peserta saja
password            VARCHAR(255) NULLABLE               -- admin saja, peserta NULL
role                VARCHAR(10) NOT NULL                -- 'admin' | 'peserta'
is_active           BOOLEAN NOT NULL DEFAULT true
remember_token      VARCHAR(100) NULLABLE               -- Breeze
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

> **Aturan bisnis:** Admin → email & password diisi, nik NULL. Peserta → nik diisi, email NULL, password NULL. Kedua kolom (email dan nik) unique di level DB dengan PostgreSQL NULL semantics (NULL tidak dianggap duplikat).

### Tabel: participants

```
id                  BIGSERIAL PRIMARY KEY
user_id             BIGINT NOT NULL FK → users(id) ON DELETE CASCADE
assigned_admin_id   BIGINT NULLABLE FK → users(id) ON DELETE SET NULL
name                VARCHAR(255) NOT NULL
nik                 CHAR(16) NOT NULL                  -- copy dari users.nik, untuk query cepat
address             TEXT NULLABLE
phone               VARCHAR(20) NULLABLE
violation_type      VARCHAR(255) NOT NULL               -- jenis pelanggaran
case_notes          TEXT NULLABLE                       -- catatan kasus dari petugas
supervision_start   DATE NOT NULL
supervision_end     DATE NOT NULL
quota_type          VARCHAR(10) NOT NULL                -- 'weekly' | 'monthly'
quota_amount        INTEGER NOT NULL                    -- jumlah wajib lapor per periode
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

### Tabel: locations

```
id                  BIGSERIAL PRIMARY KEY
name                VARCHAR(255) NOT NULL
address             TEXT NULLABLE
latitude            DECIMAL(10,8) NOT NULL
longitude           DECIMAL(11,8) NOT NULL
radius_meters       INTEGER NOT NULL DEFAULT 100
is_active           BOOLEAN NOT NULL DEFAULT true
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

### Tabel: participant_location (Pivot)

```
id                  BIGSERIAL PRIMARY KEY
participant_id      BIGINT NOT NULL FK → participants(id) ON DELETE CASCADE
location_id         BIGINT NOT NULL FK → locations(id) ON DELETE CASCADE
created_at          TIMESTAMP
updated_at          TIMESTAMP

UNIQUE(participant_id, location_id)     -- mencegah duplikasi assignment
```

> **Aturan bisnis:** Setiap peserta ditetapkan lokasi wajib lapor oleh admin. Jumlah lokasi yang ditetapkan harus sesuai dengan `quota_amount`. Saat absensi, peserta hanya bisa absen di lokasi yang sudah ditetapkan untuknya, bukan di semua lokasi aktif.

### Tabel: attendance_periods

```
id                  BIGSERIAL PRIMARY KEY
participant_id      BIGINT NOT NULL FK → participants(id) ON DELETE CASCADE
period_start        DATE NOT NULL
period_end          DATE NOT NULL
quota_target        INTEGER NOT NULL                    -- snapshot quota_amount saat periode dibuat
created_at          TIMESTAMP
```

> `quota_target` disimpan sebagai snapshot — jika admin mengubah `quota_amount` peserta di tengah jalan, periode yang sudah berjalan tidak terpengaruh.

### Tabel: attendance_logs

```
id                  BIGSERIAL PRIMARY KEY
participant_id      BIGINT NOT NULL FK → participants(id)
location_id         BIGINT NULLABLE FK → locations(id) ON DELETE SET NULL
period_id           BIGINT NOT NULL FK → attendance_periods(id)
attended_at         DATE NOT NULL                       -- tanggal hadir (bukan timestamp)
latitude            DECIMAL(10,8) NULLABLE
longitude           DECIMAL(11,8) NULLABLE
accuracy_meters     DECIMAL(8,2) NULLABLE
photo_path          VARCHAR(500) NULLABLE
is_manual_override  BOOLEAN NOT NULL DEFAULT false
override_reason     TEXT NULLABLE
override_by         BIGINT NULLABLE FK → users(id)
created_at          TIMESTAMP

UNIQUE(participant_id, attended_at)                    -- enforce 1 absensi per hari
```

### Tabel: attendance_attempts

```
id                  BIGSERIAL PRIMARY KEY
participant_id      BIGINT NOT NULL FK → participants(id)
attempted_at        TIMESTAMP NOT NULL
latitude            DECIMAL(10,8) NULLABLE
longitude           DECIMAL(11,8) NULLABLE
accuracy_meters     DECIMAL(8,2) NULLABLE
nearest_location_id BIGINT NULLABLE FK → locations(id)
distance_meters     DECIMAL(10,2) NULLABLE
rejection_reason    VARCHAR(255) NOT NULL               -- kode alasan penolakan
created_at          TIMESTAMP
```

### Tabel: warnings

```
id                  BIGSERIAL PRIMARY KEY
participant_id      BIGINT NOT NULL FK → participants(id)
triggered_period_id BIGINT NOT NULL FK → attendance_periods(id)
level               SMALLINT NOT NULL                   -- 1, 2, atau 3
message             TEXT NOT NULL
is_resolved         BOOLEAN NOT NULL DEFAULT false
resolved_at         TIMESTAMP NULLABLE
created_at          TIMESTAMP
```

### Tabel: activity_logs

```
id                  BIGSERIAL PRIMARY KEY
user_id             BIGINT NOT NULL FK → users(id)
action              VARCHAR(100) NOT NULL               -- misal: 'POST admin.participants.store'
target_type         VARCHAR(50) NULLABLE                -- misal: 'Participant'
target_id           BIGINT NULLABLE
description         TEXT NULLABLE
ip_address          VARCHAR(45) NULLABLE
created_at          TIMESTAMP
```

---

## 9. Sistem Peringatan

Dijalankan otomatis oleh **Laravel Scheduler** setiap hari pukul 08.00 WIB.

### Logika Trigger

**Level 1 — Peringatan di Dashboard**
```
Kondisi: sisa hari periode ≤ 3 hari DAN sisa kewajiban > 0
Aksi: Buat record warning level 1 jika belum ada yang aktif untuk periode ini
Tampil: Notifikasi di dashboard peserta (warna kuning)
Email: Tidak dikirim
```

**Level 2 — Catatan Mangkir**
```
Kondisi: Periode baru saja berakhir (period_end = kemarin) DAN sisa kewajiban > 0
Aksi: Buat record warning level 2
Tampil: Peringatan merah di dashboard peserta
Email: Dikirim ke admin penanggung jawab (assigned_admin_id)
```

**Level 3 — Wajib Hadir Langsung**
```
Kondisi: Ada 2 warning level 2 aktif (is_resolved = false) secara berturut-turut
Aksi: Buat record warning level 3
Tampil: Alert merah mencolok di dashboard peserta
Email: Dikirim ke semua admin (broadcast)
```

### Catatan Penting
- Setiap level hanya dibuat satu kali per periode (tidak duplikat)
- Admin dapat menandai warning sebagai `is_resolved = true` dari halaman detail peserta
- Riwayat semua warning tersimpan dan tidak dihapus

---

## 10. Dashboard Peserta

Tampilan setelah peserta login dengan NIK.

**Card Informasi Utama:**
- Nama lengkap peserta
- Jenis pelanggaran
- Status pengawasan: `AKTIF` (hijau) atau `SELESAI` (abu-abu)
- Masa pengawasan: tanggal mulai s.d. tanggal selesai + sisa hari
- Target periode ini: "X dari Y kehadiran terpenuhi"
- Sisa kewajiban: "Masih perlu hadir N kali"

**Tombol Absensi:**
- Label: "Absensi Sekarang"
- Aktif jika: masa pengawasan aktif + belum absen hari ini + kuota periode belum penuh
- Disabled dengan pesan jika salah satu kondisi tidak terpenuhi

**Peta Lokasi:**
- Leaflet.js menampilkan semua lokasi resmi aktif
- Setiap lokasi tampil dengan marker + circle radius
- Peserta bisa tap marker untuk lihat nama dan alamat lokasi

**Riwayat Absensi:**
- 30 entri terakhir dalam format tabel
- Kolom: Tanggal, Lokasi, Status (Normal / Input Manual)
- Warna berbeda untuk entri manual override

**Peringatan Aktif:**
- Ditampilkan jika ada warning yang belum resolved
- Level 1: banner kuning di atas dashboard
- Level 2 & 3: banner merah mencolok

---

## 11. Dashboard Admin

**Statistik Ringkas (Card di atas):**
- Total peserta aktif masa pengawasan
- Peserta patuh (sudah penuhi kuota periode ini)
- Peserta berisiko (sisa hari ≤ 3 dan masih ada kewajiban)
- Peserta mangkir (periode lalu tidak memenuhi kuota)
- Peserta selesai masa pengawasan dalam 7 hari ke depan

**Tabel Peserta:**
- Filter: status (aktif/selesai), jenis pelanggaran, tingkat kepatuhan
- Search: nama atau NIK
- Kolom: Nama, NIK, Pelanggaran, Periode Aktif, Kehadiran/Target, Status Kepatuhan, Aksi
- Pagination

**Halaman Detail Peserta:**
- Semua informasi peserta
- Progress kehadiran periode ini (progress bar)
- Tabel riwayat absensi lengkap + thumbnail foto selfie (klik untuk lihat full)
- Tabel percobaan absensi yang ditolak (untuk audit)
- Form override manual absensi (alasan wajib diisi)
- Riwayat peringatan

**Manajemen Lokasi:**
- Daftar semua lokasi dengan status aktif/nonaktif
- Form tambah lokasi dengan peta Leaflet interaktif (klik peta → koordinat terisi otomatis)
- Visualisasi radius sebagai circle di peta
- Toggle aktif/nonaktif lokasi

**Laporan:**
- Filter berdasarkan periode, status kepatuhan
- Tabel rangkuman semua peserta
- Laporan detail per peserta dengan seluruh riwayat absensi
- Print-ready via `window.print()` dengan CSS `@media print`

---

## 12. Konfigurasi Database Lokal — Laragon + PostgreSQL

Laragon menyertakan PostgreSQL secara built-in. Konfigurasi yang digunakan:

```
Host     : 127.0.0.1
Port     : 5432
Database : wajib_lapor_db
Username : postgres
Password : (kosong atau sesuai yang diset di Laragon)
```

**File .env Laravel:**
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=wajib_lapor_db
DB_USERNAME=postgres
DB_PASSWORD=
```

Untuk membuat database, gunakan salah satu:
- HeidiSQL (sudah bundel di Laragon) → koneksi ke PostgreSQL → buat database baru
- pgAdmin (jika terinstall terpisah)
- Command line: `psql -U postgres -c "CREATE DATABASE wajib_lapor_db;"`

---

## 13. Keamanan Sistem

| Aspek | Implementasi |
|---|---|
| Auth admin | Laravel Breeze — session-based, CSRF built-in |
| Auth peserta | Custom — NIK lookup, session sederhana |
| Brute force login | Rate limiting: max 5 percobaan login per IP per 10 menit |
| Role-based access | Middleware terpisah untuk setiap prefix route |
| CSRF | Aktif default di semua form Laravel |
| Rate limiting absensi | Throttle: max 10 percobaan per hari per user |
| Upload file | Validasi MIME type server-side, storage private (tidak publik) |
| Akses foto selfie | Hanya via route admin yang dilindungi middleware, bukan direct URL |
| Audit log | Semua aksi POST/PUT/DELETE admin tercatat otomatis |
| Session timeout | Auto logout setelah 30 menit tidak aktif |
| SQL injection | Aman via Eloquent ORM (parameterized query) |

---

## 14. Struktur Folder Proyek

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                          ← Breeze (admin)
│   │   │   └── AuthenticatedSessionController.php
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── ParticipantController.php
│   │   │   ├── LocationController.php
│   │   │   ├── AttendanceController.php   ← override manual + lihat attempts
│   │   │   └── ReportController.php
│   │   ├── Participant/
│   │   │   ├── DashboardController.php
│   │   │   ├── AbsenceController.php
│   │   │   └── HistoryController.php
│   │   └── PesertaAuthController.php      ← login/logout peserta (NIK only)
│   └── Middleware/
│       ├── RoleMiddleware.php             ← cek role, redirect jika salah
│       └── LogActivityMiddleware.php      ← catat aksi admin ke activity_logs
│
├── Models/
│   ├── User.php
│   ├── Participant.php
│   ├── Location.php
│   ├── AttendancePeriod.php
│   ├── AttendanceLog.php
│   ├── AttendanceAttempt.php
│   ├── Warning.php
│   └── ActivityLog.php
│
├── Services/
│   ├── AttendanceService.php              ← validasi 8 langkah + Haversine
│   ├── PeriodService.php                  ← generate & manage periode
│   └── WarningService.php                 ← generate & kirim peringatan
│
├── Console/
│   └── Commands/
│       ├── CheckAttendanceWarnings.php    ← scheduler harian jam 08.00
│       └── GenerateNextPeriods.php        ← scheduler harian jam 00.05
│
└── Mail/
    └── WarningNotificationMail.php        ← template email peringatan

resources/
└── views/
    ├── auth/                              ← Breeze admin login (dikustomisasi)
    │   └── login.blade.php
    ├── peserta-auth/
    │   └── login.blade.php                ← Login peserta (NIK only)
    ├── layouts/
    │   ├── admin.blade.php                ← Layout sidebar admin
    │   └── participant.blade.php          ← Layout peserta
    ├── admin/
    │   ├── dashboard.blade.php
    │   ├── participants/
    │   │   ├── index.blade.php
    │   │   ├── create.blade.php
    │   │   ├── edit.blade.php
    │   │   └── show.blade.php
    │   ├── locations/
    │   │   ├── index.blade.php
    │   │   ├── create.blade.php
    │   │   └── edit.blade.php
    │   ├── attendance/
    │   │   └── attempts.blade.php
    │   └── reports/
    │       ├── index.blade.php
    │       └── show.blade.php
    └── participant/
        ├── dashboard.blade.php
        ├── absence.blade.php
        └── history.blade.php

routes/
├── web.php                                ← Semua route
├── auth.php                               ← Breeze auth routes (dikustomisasi prefix /admin)
└── console.php                            ← Scheduler commands

database/
├── migrations/                            ← 8 migration files
└── seeders/
    ├── DatabaseSeeder.php
    ├── AdminSeeder.php
    ├── LocationSeeder.php
    └── ParticipantSeeder.php
```

---

## 15. Rencana Pengembangan Bertahap

### Phase 1 — Core (MVP)
- Setup Laravel 13 + Breeze + PostgreSQL Laragon
- Dua jalur login terpisah (Admin: Breeze, Peserta: NIK only)
- CRUD peserta dengan generate periode otomatis
- CRUD lokasi resmi dengan peta Leaflet interaktif
- Absensi GPS + selfie dengan 8 validasi server-side
- Log percobaan absensi (berhasil & ditolak)
- Dashboard peserta (status, peta, riwayat)
- Dashboard admin (statistik, kelola data)

### Phase 2 — Enforcement
- Scheduler harian: generate periode + cek peringatan
- Sistem peringatan Level 1, 2, 3
- Email notifikasi Level 2 & 3
- Override manual absensi + audit log lengkap
- Halaman laporan kepatuhan (HTML print-ready)

### Phase 3 — Enhancement (Opsional, setelah v1 stabil)
- Export PDF (Dompdf)
- Notifikasi WhatsApp via gateway murah (Fonnte/Wablas)
- Multi-unit/Polsek (tambah kolom `unit_id` di tabel terkait)

---

## 16. Hal yang Sengaja Tidak Dimasukkan

| Fitur | Alasan |
|---|---|
| Password untuk peserta | Desain yang dipilih: NIK saja untuk kemudahan peserta |
| Self-register peserta | Semua peserta diregistrasi admin secara fisik |
| QR Code absensi | Butuh cetak fisik dan koordinasi tambahan |
| Face recognition | Tidak proporsional untuk skala Polres |
| Mobile app native | Web responsive sudah cukup, hindari dual maintenance |
| Vue/React | Blade + Alpine.js sudah cukup untuk interaksi yang dibutuhkan |
| WhatsApp di v1 | Ditunda ke Phase 3, email cukup untuk v1 |

---

## 17. Nama Formal Proyek

**Rancang Bangun Sistem Informasi Wajib Lapor Digital Berbasis Web Menggunakan Laravel 13, PostgreSQL, dan Validasi Geolocation pada Tingkat Polres**

---

## 18. Kesimpulan

Sistem ini dirancang dengan prinsip **cukup, realistis, dan sesuai kondisi lapangan**. Laravel Breeze menangani autentikasi admin secara solid tanpa perlu bangun dari nol. Login peserta dengan NIK saja memberikan kemudahan maksimal bagi pengguna yang tidak melek teknologi. Dua halaman login yang terpisah secara domain mempertegas pemisahan peran dan mencegah kebingungan. Database PostgreSQL via Laragon siap dipakai di lingkungan lokal tanpa konfigurasi rumit. Seluruh logika bisnis yang kritis — kuota periode, validasi Haversine, sistem peringatan — dikapsulasi dalam Service classes yang terpisah dari Controller, sehingga mudah di-test dan di-maintain.
